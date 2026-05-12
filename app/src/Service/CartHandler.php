<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartHandler
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepo,
        private Security $security,
        private CartRepository $cartRepo,
        private EntityManagerInterface $em,
    ) {}

    private function getSession()
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }

    private function getUser(): ?User
    {
        $user = $this->security->getUser();
        return $user instanceof User ? $user : null;
    }

    private function getOpenCart(): ?Cart
    {
        $user = $this->getUser();
        if (!$user) return null;
        return $this->cartRepo->findOpenCartWithLines($user);
    }

    private function getOrCreateCart(): Cart
    {
        $cart = $this->getOpenCart();
        if ($cart) return $cart;

        $cart = (new Cart())
            ->setUser($this->getUser())
            ->setStatus(Cart::OPEN);
        $this->em->persist($cart);
        return $cart;
    }

    private function findCartLine(Cart $cart, int $productId): ?CartLine
    {
        foreach ($cart->getCartLines() as $cartLine) {
            if ($cartLine->getProduct()->getId() === $productId) {
                return $cartLine;
            }
        }
        return null;
    }

    public function getCart(): array
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->getSession()->get('cart', []);
        }

        $cart = $this->getOpenCart();
        if (!$cart) return [];

        $result = [];
        foreach ($cart->getCartLines() as $cartLine) {
            $result[$cartLine->getProduct()->getId()] = $cartLine->getQuantity();
        }
        return $result;
    }

    public function addToCart(string $id): void
    {
        $user = $this->getUser();

        if (!$user) {
            $cart = $this->getSession()->get('cart', []);
            $cart[$id] = ($cart[$id] ?? 0) + 1;
            $this->getSession()->set('cart', $cart);
            return;
        }

        $cart = $this->getOrCreateCart();
        $cartLine = $this->findCartLine($cart, (int) $id);

        if ($cartLine) {
            $cartLine->setQuantity($cartLine->getQuantity() + 1);
        } else {
            $product = $this->productRepo->find($id);
            $cartLine = (new CartLine())
                ->setCart($cart)
                ->setProduct($product)
                ->setQuantity(1);
            $this->em->persist($cartLine);
        }
        $this->em->flush();
    }

    public function decrease(string $id): void
    {
        $user = $this->getUser();

        if (!$user) {
            $cart = $this->getSession()->get('cart', []);
            if (isset($cart[$id]) && $cart[$id] > 1) {
                $cart[$id]--;
            } else {
                $cart[$id] = 1;
            }
            $this->getSession()->set('cart', $cart);
            return;
        }

        $cart = $this->getOpenCart();
        if (!$cart) return;

        $cartLine = $this->findCartLine($cart, (int) $id);
        if (!$cartLine) return;

        if ($cartLine->getQuantity() > 1) {
            $cartLine->setQuantity($cartLine->getQuantity() - 1);
            $this->em->flush();
        }
    }

    public function clearCart(): void
    {
        $user = $this->getUser();

        if (!$user) {
            $this->getSession()->set('cart', []);
            return;
        }

        $cart = $this->getOpenCart();
        if (!$cart) return;

        foreach ($cart->getCartLines() as $cartLine) {
            $this->em->remove($cartLine);
        }
        $this->em->flush();
    }

    public function convertCart(): void
    {
        $user = $this->getUser();

        if (!$user) {
            $this->getSession()->set('cart', []);
            return;
        }

        $cart = $this->getOpenCart();
        if (!$cart) return;

        $cart->setStatus(Cart::CONVERTED);
        $this->em->flush();
    }

    public function getCartProducts(): array
    {
        $cartProducts = [];
        foreach ($this->getCart() as $id => $qty) {
            $cartProducts[] = $this->productRepo->find($id);
        }
        return $cartProducts;
    }

    public function getTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->getCart() as $id => $qty) {
            $totalPrice += $this->productRepo->find($id)->getPrice() * $qty;
        }
        return $totalPrice;
    }

}
