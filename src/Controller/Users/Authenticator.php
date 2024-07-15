<?php

namespace App\Controller\Users;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class Authenticator{

    public function __construct(
        private HttpClientInterface $apiClient,
        private Session $session,
        private RouterInterface $router,
        private Environment $twig,
        private $apiLogin
    )
    {}

    #[Route('/auth', name:"app_auth", methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $response = $this->apiClient->request('POST', $this->apiLogin, [
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);

        $data = $response->toArray();

        if (isset($data['token'])) {
            $this->session->set('jwt_token', $data['token']);
            $url = $this->router->generate('app_home');
            return new RedirectResponse($url);
        } else {
            $content = $this->twig->render('users/loginPage.html.twig', [
                'error' => 'Erreur de connexion, veuillez réessayer.'
            ]);
            return new Response($content);
        }
    }
}