<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Order;
use App\Form\AdressType;
use App\Service\CartHandler;
use App\Service\OrderHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/adress', name: 'app_order_addAdress')]
    public function AddAdress(Request $request, OrderHandler $orderHandler, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdressType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $orderHandler->makeOrder($this->getUser());

            $adress = $form->getData();
            $adress->setCustomerOrder($order);

            $em->persist($adress);
            $em->flush();

            return $this->redirectToRoute('app_product_index');
        }
        return $this->render('order/add-adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
