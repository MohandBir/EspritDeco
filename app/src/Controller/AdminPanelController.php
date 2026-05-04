<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\Role;

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
