<?php

namespace App\Security;

use App\HttpClient\ApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CustomLogin extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private RouterInterface $router,
        private HttpClientInterface $apiClient,
        private CustomUserProvider $customUserProvider
    )
    {}

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $response = $this->apiClient->request('POST', ApiService::LOGIN->value, [
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $data = $response->toArray();
        $jwtToken = $data['token'];
        $session = $request->getSession();
        $session->set('jwt_token', $jwtToken);

        return new SelfValidatingPassport(
            new UserBadge($email, function ($userIdentifier) use ($jwtToken) {
                return $this->customUserProvider->loadUserByIdentifierAndToken($userIdentifier, $jwtToken);
            }),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_login');
    }
}