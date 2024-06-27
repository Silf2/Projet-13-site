<?php

namespace App\Controller\Products;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController()]
final class AllProductsHomepage
{
    public function __construct(
        private Environment $twig,
    )
    {}

    #[Route('/', name: "app_home")]
    public function __invoke(): Response
    {
        $content = $this->twig->render("products/homepage.html.twig");

        $content = $this->twig->render("products/homepage.html.twig");
        return new Response($content);
    }
}