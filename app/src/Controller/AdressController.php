<?php

namespace App\Controller;

use App\Form\AdressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdressController extends AbstractController
{
    #[Route('/adress', name: 'app_adress_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(AdressType::class);
        $form->handleRequest($request);

        return $this->render('adress/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
