<?php

namespace App\Controller\Cart;

use App\HttpClient\ApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class SubmitCart
{
    public function __construct(
        private Environment $twig,
        private HttpClientInterface $apiClient,
        private RouterInterface $router
    )
    {}

    #[Route("app/cart/submit", name: "app_submitCart")]
    public function __invoke(Request $request): RedirectResponse
    {
        $session = $request->getSession();

        if (!$session->get('cart'))
        {
            return new RedirectResponse($this->router->generate('app_cart'));
        }

        $data = ['assocProductOrders' => []];
        $jwtToken = $session->get('jwt_token');
        $cart = $session->get('cart');

        foreach ($cart as $item) {
            $productInfo = [
                'product' => "/api/products/{$item['id']}",
                'quantity' => (int) $item['quantity']
            ];

            $data['assocProductOrders'][] = $productInfo;
        }

        $response = $this->apiClient->request('POST', ApiService::ORDER->value, [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $jwtToken
            ]
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Echec de la création de la commande');
        }

        $session->remove('cart');

        return new RedirectResponse($this->router->generate('app_cart', ['orderSuccess' => 1]));
    }
}