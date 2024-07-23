<?php 

namespace App\Controller\Cart;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

#[AsController()]
final class AddToCart
{
    public function __construct(
        private RouterInterface $router
    )
    {}

    #[Route('/cart/add/{id}',  name: 'app_addToCart')]
    public function __invoke(int $id, Request $request)
    {
        $session = $request->getSession();
        $quantity = $request->request->get('quantity');

        $cart = $session->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + $quantity;

        $session->set('cart', $cart);

        return new RedirectResponse($this->router->generate('app_product', ['id' => $id]));
    }
}
