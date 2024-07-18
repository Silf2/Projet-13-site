<?php

namespace App\Controller\Cart;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

#[AsController()]
final class ClearCart
{
    public function __construct(
        private RouterInterface $router
    )
    {}

    #[Route('/cart/clear', name: 'app_clearCart')]
    public function __invoke(Request $request): RedirectResponse
    {
        $session = $request->getSession();

        $session->remove('cart');

        return new RedirectResponse($this->router->generate('app_cart'));
    }
}