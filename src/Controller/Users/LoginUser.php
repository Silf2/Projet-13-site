<?php

namespace App\Controller\Users;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

#[AsController()]
final class LoginUser
{
    public function __construct(
        //private AuthenticationUtils $authenticationUtils,
        private Environment $twig
        )
    {}
    #[Route('/login', name: 'app_login')]
    public function __invoke(): Response
    {

        $content = $this->twig->render("users/loginPage.html.twig");

        return new Response($content);
    }
}