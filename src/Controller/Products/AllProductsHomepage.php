<?php

namespace App\Controller\Products;

use App\HttpClient\ApiService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class AllProductsHomepage
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $apiClient,
    )
    {}

    #[Route('/', name: "app_home", methods: ['GET'])]
    public function __invoke(): Response
    {
        $response = $this->apiClient->request('GET', ApiService::ALL_PRODUCT->value);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Echec de la récupération des produits');
        }

        $productsData = $response->toArray();
        $products = $productsData['hydra:member'];

        $content = $this->twig->render("products/homepage.html.twig", [ 
            'products' => $products,
        ]);
        
        return new Response($content);
    }
}