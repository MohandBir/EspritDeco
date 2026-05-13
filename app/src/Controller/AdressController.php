<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdressController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/adress', name: 'app_adress_index', methods: ['GET'])]
    public function index(): Response
    {
        $form = $this->createForm(AdressType::class);

        return $this->render('adress/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/adress/edit/{id}', name: 'app_adress_edit', methods: ['GET', 'POST'])]
    public function edit(Adress $adress, Request $request): Response
    {
        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_order_recap', [
                'id' => $adress->getCustomerOrder()->getId(),
            ]);
        }

        return $this->render('adress/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
