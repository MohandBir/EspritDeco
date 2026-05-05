<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminPanelController extends AbstractController
{
    #[Route('/admin/panel', name: 'app_admin_index')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('admin/index.html.twig');
    }
}
