<?php

namespace App\Controller\Cart;

use App\HttpClient\ApiService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class CartPage
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $apiClient
    )
    {}

    #[Route('/cart', name: 'app_cart')]
    public function __invoke(Request $request): Response
    {
        $session = $request->getSession();

        if (!$session->get('cart'))
        {
            $content = $this->twig->render("products/cart.html.twig");
            return new Response($content);
        }

        $totalPrice = 0;
        $products = [];
        $cart = $session->get('cart');

        foreach ($cart as $id => $quantity) {
            $apiUrl = sprintf(ApiService::ONE_PRODUCT->value, $id);
            $response = $this->apiClient->request('GET', $apiUrl);
           
            if ($response->getStatusCode() !== 200) {
                throw new Exception('Echec de la récupération du produit');
            }

            $product = $response->toArray();
            $product['quantity'] = $quantity;
            $totalPrice += $product['price'] * $product['quantity'];

            $products[] = $product;
        }

        $content = $this->twig->render("products/cart.html.twig", [
            "products" => $products,
            "totalPrice" => $totalPrice
        ]);

        return new Response($content);
    }
}
