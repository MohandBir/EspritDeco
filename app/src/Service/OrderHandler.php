<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class OrderHandler 
{
    public function __construct(
        private CartHandler $cartHandler,
        private EntityManagerInterface $em,
    )
    {
    }

    public function makeOrder(User $user)
    {
        $cart = $this->cartHandler->getCart();

        if (!empty($cart)) {
            $totalAmount = $this->cartHandler->getTotalPrice();
            $order = (new Order())
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setStatus(Order::PENDING_PAYEMENT)
                ->setTotalAmount($totalAmount)
                ->setUser($user)
            ;
            
            $products = $this->cartHandler->getCartProducts();
            $i = 0;
            foreach ($cart as $id => $qty) {
                $orderLine = (new OrderLine())
                    ->setCustomerOrder($order)
                    ->setProduct($products[$i])
                    ->setQuantity($qty)
                    ->setUnitPrice($products[$i]->getPrice())
                ;
                $this->em->persist($orderLine);
                $i++;
            }
            $this->em->persist($order);
            $this->em->flush();
        }
        
        return $order;
    }

}
