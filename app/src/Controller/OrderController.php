<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Order;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use App\Repository\OrderRepository;
use App\Service\CartHandler;
use App\Service\OrderHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private AdressRepository $adressRepo,
        private OrderRepository $orderRepo,
        private EntityManagerInterface $em,
    ){}
    #[Route('/adress', name: 'app_order_addAdress')]
    public function AddAdress(Request $request, OrderHandler $orderHandler): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $savedOrder = $this->orderRepo->findOneBy(['user' => $this->getUser(), 'status' => Order::PENDING_PAYEMENT]);
        $savedAdress = $savedOrder ? $savedOrder->getAdress() : null;

        if ($savedOrder && !$request->isMethod('POST')) {
            $orderHandler->loadOrderCart($savedOrder);
        }

        $form = $this->createForm(AdressType::class, $savedAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($savedOrder) {
                $order = $orderHandler->updateOrder($this->getUser(), $savedOrder);
                if (!$savedAdress) {
                    $adress = $form->getData();
                    $adress->setCustomerOrder($order);
                    $this->em->persist($adress);
                }
            } else {
                $order = $orderHandler->makeOrder($this->getUser());
                $adress = $form->getData();
                $adress->setCustomerOrder($order);
                $this->em->persist($adress);
            }

            $this->em->flush();

            return $this->redirectToRoute('app_product_index');
        }
        
        return $this->render('order/add-adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
