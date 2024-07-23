<?php

namespace App\Controller\Users;

use App\HttpClient\ApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsController]
final class DeleteUser
{
    public function __construct(
        private HttpClientInterface $apiClient, 
        private RouterInterface $router
    )
    {}

    #[Route('/users/delete', name:'app_deleteUser')]
    public function __invoke(Request $request)
    {
        $session = $request->getSession();
        $jwtToken = $session->get('jwt_token');

        $response = $this->apiClient->request('DELETE', ApiService::REGISTER->value, [
            'headers' => [
                'Authorization' => 'Bearer ' . $jwtToken
            ]
        ]);

        //dd($response->getContent());

        if ($response->getStatusCode() !== 204) {
            throw new \Exception('Echec de la suppression');
        }

        $session->clear();

        return new RedirectResponse($this->router->generate('app_logout'));
    }
}