<?php

namespace App\Controller\Users;

use App\Entity\User;
use App\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

#[AsController()]
final class RegisterPage
{
    public function __construct(
        private Environment $twig,
        private FormFactoryInterface $formFactory,
        private UrlGeneratorInterface $urlGenerator
    )
    {}

    #[Route('/registerPage', name: "app_registerPage")]
    public function __invoke(Request $request): Response
    {
        $content = $this->twig->render("users/registerPage.html.twig");

        return new Response($content);
    }
}