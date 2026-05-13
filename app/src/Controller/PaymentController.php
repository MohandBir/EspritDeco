<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\OrderMailer;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private StripeService $stripeService,
        private LoggerInterface $logger,
    ) {}

    #[Route('/order/{id}/checkout', name: 'app_payment_checkout')]
    public function checkout(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($order->getStatus() === Order::PAID) {
            $this->addFlash('warning', 'Cette commande a déjà été payée.');
            return $this->redirectToRoute('app_order_recap', ['id' => $order->getId()]);
        }

        $successUrl = $this->generateUrl(
            'app_payment_success',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        ) . '?session_id={CHECKOUT_SESSION_ID}';

        $cancelUrl = $this->generateUrl(
            'app_payment_cancel',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        try {
            $session = $this->stripeService->createCheckoutSession($order, $successUrl, $cancelUrl);
        } catch (\Exception $e) {
            $this->logger->error('Stripe session creation failed', [
                'order_id' => $order->getId(),
                'error'    => $e->getMessage(),
            ]);
            $this->addFlash('danger', 'Une erreur est survenue lors de la connexion au service de paiement.');
            return $this->redirectToRoute('app_order_recap', ['id' => $order->getId()]);
        }

        $order->setStripeSessionId($session->id);
        $this->em->flush();

        $this->logger->info('Stripe checkout session created', [
            'order_id'   => $order->getId(),
            'session_id' => $session->id,
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/order/{id}/success', name: 'app_payment_success')]
    public function success(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('payment/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/cancel', name: 'app_payment_cancel')]
    public function cancel(Order $order, OrderMailer $orderMailer): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {
            $orderMailer->sendCancellation($order);
        } catch (\Exception $e) {
            $this->logger->error('Cancellation email failed', [
                'order_id' => $order->getId(),
                'error'    => $e->getMessage(),
            ]);
        }

        $this->addFlash('warning', 'Votre paiement a été annulé. Vous pouvez réessayer ci-dessous.');

        return $this->redirectToRoute('app_order_recap', ['id' => $order->getId()]);
    }
}
