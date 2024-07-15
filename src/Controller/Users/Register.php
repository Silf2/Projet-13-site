<?php

namespace App\Controller\Users;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class Register 
{
    public function __construct(
        private HttpClientInterface $apiClient,
        private Environment $twig,
        private string $apiRegister
    )
    {}

    #[Route('/register', name: "app_register")]
    public function __invoke(Request $request): Response
    {
        $lastName = $request->request->get('lastName');
        $firstName = $request->request->get('firstName');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $passwordConfirmation = $request->request->get('password_confirmation');
        $termsAgreed = $request->request->get('termsAgreed');

        if ($passwordConfirmation != $password || !$termsAgreed)
        {
            throw new \Exception("Les mots de passes doivent être identique. N'oubliez pas d'accepter les conditions d'utilisation.");
        }

        $data = [
            'lastName' => $lastName,
            'firstName' => $firstName,
            'email' => $email,
            'password' => $password
        ];

        $response = $this->apiClient->request('POST', $this->apiRegister, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Echec de la création de l\'utilisateur.');
        }

        $content = $this->twig->render("users/loginPage.html.twig");

        return new Response($content);
    }
}