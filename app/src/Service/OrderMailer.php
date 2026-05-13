<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailerFrom,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function sendConfirmation(Order $order): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'Esprit Déco'))
            ->to($order->getUser()->getEmail())
            ->subject('Confirmation de votre commande #' . $order->getId())
            ->htmlTemplate('email/order_confirmation.html.twig')
            ->context(['order' => $order]);

        $this->mailer->send($email);
    }

    public function sendCancellation(Order $order): void
    {
        $recapUrl = $this->urlGenerator->generate(
            'app_order_recap',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'Esprit Déco'))
            ->to($order->getUser()->getEmail())
            ->subject('Votre paiement a été annulé — commande #' . $order->getId())
            ->htmlTemplate('email/order_cancellation.html.twig')
            ->context([
                'order'    => $order,
                'recapUrl' => $recapUrl,
            ]);

        $this->mailer->send($email);
    }
}
