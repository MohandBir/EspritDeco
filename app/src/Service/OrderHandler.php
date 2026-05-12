<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Repository\OrderLineRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class OrderHandler 
{
    public function __construct(
        private CartHandler $cartHandler,
        private EntityManagerInterface $em,
        private OrderLineRepository $orderLineRepo,
    )
    {
    }

    public function makeOrder(User $user)
    {
        $cart = $this->cartHandler->getCart();

        if (empty($cart)) return;

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
        
        
        return $order;
    }

    public function loadOrderCart(Order $savedOrder): void
    {
        if (!empty($this->cartHandler->getCart())) {
            return;
        }

        $orderLines = $this->orderLineRepo->findWithProduct($savedOrder);
        $cart = $this->cartHandler->getSavedCart($orderLines);
        $this->cartHandler->setCart($cart);
    }

    public function updateOrder(User $user, Order $savedOrder): Order
    {
        $cart = $this->cartHandler->getCart();

        if (empty($cart)) return $savedOrder;

        foreach ($savedOrder->getOrderLines() as $orderLine) {
            $this->em->remove($orderLine);
        }

        $totalAmount = $this->cartHandler->getTotalPrice();
        $savedOrder->setTotalAmount($totalAmount);

        $products = $this->cartHandler->getCartProducts();
        $i = 0;
        foreach ($cart as $qty) {
            $orderLine = (new OrderLine())
                ->setCustomerOrder($savedOrder)
                ->setProduct($products[$i])
                ->setQuantity($qty)
                ->setUnitPrice($products[$i]->getPrice())
            ;
            $this->em->persist($orderLine);
            $i++;
        }

        $this->em->persist($savedOrder);
        $this->em->flush();

        return $savedOrder;
    }
 
}
