<?php

namespace App\Security;

use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class CustomJWTAuthenticator extends AbstractAuthenticator
{
    private $jwtConfig;

    public function __construct(
        private UserProviderInterface $userProvider,
        private string $passphrase
    )
    {
        $this->jwtConfig = Configuration::forSymmetricSigner(
            new Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText($passphrase)
        );
    }
    
    public function supports(Request $request): ?bool
    {
        return $request->getSession()->has('jtw_token');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->getSession()->get('jwt_token');
        $parsedToken = (new JwtFacade())->parse(
            $token,
            new SignedWith(new Sha256(), \Lcobucci\JWT\Signer\Key\InMemory::plainText($this->passphrase)
        ),
            new StrictValidAt(
                new FrozenClock(new \DateTimeImmutable())
            )
        );

        $email = $parsedToken->claims()->get('email');

        return new SelfValidatingPassport(
            new UserBadge($email, function ($email)
            {
                return $this->userProvider->loadUserByIdentifier($email);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Authentication Failed'], Response::HTTP_UNAUTHORIZED);
    }
}