<?php

namespace App\Controller\Products;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class OneProduct
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $apiClient,
        private string $apiOneProduct
    )
    {}

    #[Route('/products/{id}', name: "app_product", methods: ['GET'])]
    public function __invoke(int $id): Response
    {
        $apiUrl = sprintf($this->apiOneProduct, $id);
        $response = $this->apiClient->request('GET', $apiUrl);
       
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Echec de la récupération du produit');
        }

        $product = $response->toArray();

        $content = $this->twig->render("products/productPage.html.twig", [ 
            'product' => $product,
        ]);
        
        return new Response($content);
    }
}