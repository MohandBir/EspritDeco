<?php

namespace App\EventSubscriber;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\OrderHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private OrderRepository $orderRepo,
        private OrderHandler $orderHandler,
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

        if (!$user instanceof User) {
            return;
        }

        $savedOrder = $this->orderRepo->findOneBy([
            'user' => $user,
            'status' => Order::PENDING_PAYEMENT,
        ]);

        if ($savedOrder) {
            $this->orderHandler->loadOrderCart($savedOrder);
        }
    }
}
