<?php

namespace App\Controller;

use App\Service\CartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function add(string $id, CartHandler $cartHandler): Response
    {
        // $session->remove('cart');
       
        $cartHandler->addCart($id);

        $this->addFlash('success', 'Le produit est ajouté au panier avec succès');

        return $this->redirectToRoute('app_product_index');
    }
}
