<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrderMailer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    public function __construct(
        private string $stripeWebhookSecret,
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em,
        private OrderMailer $orderMailer,
        private LoggerInterface $logger,
    ) {}

    #[Route('/webhook/stripe', name: 'app_webhook_stripe', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature', '');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->stripeWebhookSecret);
        } catch (SignatureVerificationException $e) {
            $this->logger->warning('Stripe webhook signature invalid', ['error' => $e->getMessage()]);
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        } catch (\UnexpectedValueException $e) {
            $this->logger->warning('Stripe webhook payload invalid', ['error' => $e->getMessage()]);
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        if ($event->type !== 'checkout.session.completed') {
            return new Response('Event ignored', Response::HTTP_OK);
        }

        $session = $event->data->object;
        $orderId = (int) ($session->metadata->order_id ?? 0);

        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            $this->logger->error('Webhook: order not found', ['order_id' => $orderId]);
            return new Response('Order not found', Response::HTTP_NOT_FOUND);
        }

        if ($order->getStatus() === Order::PAID) {
            $this->logger->info('Webhook: order already paid (idempotence)', ['order_id' => $orderId]);
            return new Response('Already processed', Response::HTTP_OK);
        }

        $order->setStatus(Order::PAID);
        $this->em->flush();

        $this->logger->info('Webhook: order marked as paid', ['order_id' => $orderId]);

        try {
            $this->orderMailer->sendConfirmation($order);
        } catch (\Exception $e) {
            $this->logger->error('Confirmation email failed after payment', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);
        }

        return new Response('OK', Response::HTTP_OK);
    }
}
