<?php

namespace App\Controller;

use App\Service\CartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(
        private CartHandler $cartHandler,
    )
    {
    }
    #[Route('/cart/add/{id}', name: 'app_cart_add', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function add(string $id): Response
    {  
        $this->cartHandler->addToCart($id);

        $this->addFlash('success', 'Le produit est ajouté au panier avec succès');

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function decrease(string $id): Response
    {  
        $this->cartHandler->decrease($id);

        return new Response();
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear() : Response
    {        
        $this->cartHandler->clearCart();
        
        return new Response();
        
    }
}
