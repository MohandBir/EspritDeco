<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\AdressType;
use App\Service\OrderHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    #[Route('/adress', name: 'app_order_addAdress', methods: ['POST'])]
    public function addAdress(Request $request, OrderHandler $orderHandler): Response
    {
        $form = $this->createForm(AdressType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $orderHandler->makeOrder($this->getUser());

            if (!$order) {
                $this->addFlash('warning', 'Votre panier est vide.');
                return $this->redirectToRoute('app_product_index');
            }

            $adress = $form->getData();
            $adress->setCustomerOrder($order);
            $this->em->persist($adress);
            $this->em->flush();

            return $this->redirectToRoute('app_order_recap', ['id' => $order->getId()]);
        }

        return $this->render('order/add-adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/recap/{id}', name: 'app_order_recap')]
    public function recap(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette commande.");
            return $this->redirectToRoute('app_product_index');
        }

        if ($order->getStatus() !== Order::PENDING_PAYEMENT) {
            $this->addFlash('danger', "Cette commande n'est plus disponible au paiement.");
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('order/recap.html.twig', [
            'order' => $order,
        ]);
    }
}
