<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartHandler 
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepo,
    )
    {
    }

    private function getSession()
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }

    public function addToCart(string $id) : void
    {
        $cart = $this->getSession()->get('cart', [] );

        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->getSession()->set('cart', $cart);
    }

    public function decrease(string $id) : void
    {
        $cart = $this->getSession()->get('cart', [] );

        if (isset($cart[$id]) && $cart[$id] > 1) {
            $cart[$id]--;
        } else {
            $cart[$id] = 1;
        }
        $this->getSession()->set('cart', $cart);
    }

    public function getCart()
    {
        return $this->getSession()->get('cart', []);
    }

    public function getCartProducts(): array
    {
        $cart = $this->getSession()->get('cart', []);
        $cartProducts = [];
        if (!empty($cart)) {
            foreach ($cart as $key => $value) {
                $product = $this->productRepo->find($key);
                $cartProducts[] = $product; 
            }
        }
        return $cartProducts;
    }

    public function getTotalPrice(): float
    {
        $cart = $this->getSession()->get('cart', []);
        $totalPrice  = 0;
        if (!empty($cart)) {
            foreach ($cart as $key => $value) {
                $product = $this->productRepo->find($key);
                $totalPrice += $product->getPrice() * $value;
            }
        }
        return $totalPrice;
    }

    public function clearCart(): void
    {
        $this->getSession()->set('cart', []);
    }


}