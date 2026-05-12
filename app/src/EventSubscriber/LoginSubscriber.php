<?php

namespace App\EventSubscriber;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CartRepository $cartRepo,
        private ProductRepository $productRepo,
        private EntityManagerInterface $em,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) return;

        $session = $event->getRequest()->getSession();
        $sessionCart = $session->get('cart', []);

        if (empty($sessionCart)) return;

        $cart = $this->cartRepo->findOpenCartWithLines($user);
        if (!$cart) {
            $cart = (new Cart())->setUser($user)->setStatus(Cart::OPEN);
            $this->em->persist($cart);
        }

        foreach ($sessionCart as $productId => $qty) {
            $cartLine = $this->findCartLine($cart, (int) $productId);

            if ($cartLine) {
                $cartLine->setQuantity($cartLine->getQuantity() + $qty);
            } else {
                $product = $this->productRepo->find($productId);
                if (!$product) continue;

                $cartLine = (new CartLine())
                    ->setCart($cart)
                    ->setProduct($product)
                    ->setQuantity($qty);
                $this->em->persist($cartLine);
            }
        }

        $this->em->flush();
        $session->remove('cart');
    }

    private function findCartLine(Cart $cart, int $productId): ?CartLine
    {
        foreach ($cart->getCartLines() as $line) {
            if ($line->getProduct()->getId() === $productId) {
                return $line;
            }
        }
        return null;
    }
}
