<?php

namespace App\Controller\Users;

use App\HttpClient\ApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

#[AsController()]
final class UserPage
{
    public function __construct(
        private HttpClientInterface $apiClient,
        private Environment $twig
    )
    {}

    #[Route('/users/details', name:'app_userPage')]
    public function __invoke(Request $request){  
        $session = $request->getSession();
        $jwtToken = $session->get('jwt_token');

        $response = $this->apiClient->request('GET', ApiService::ORDER->value, [
            'headers' => [
                'Authorization' => 'Bearer ' . $jwtToken
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Echec de la récupération des commande');
        }

        $orderData = $response->toArray();
        $orders = $orderData['hydra:member'];
        //dd($orders);


        $content = $this->twig->render("users/userPage.html.twig", [ 
            'orders' => $orders,
        ]);
        
        return new Response($content);
    }
}