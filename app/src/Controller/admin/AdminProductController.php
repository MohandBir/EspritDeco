<?php

namespace App\Controller\admin;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminProductController extends AbstractController
{
    #[Route('/admin/product', name: 'app_admin_product_index')]
    public function index(ProductRepository $ProductRepo): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute($this->getUser() ? 'app_product_index' : 'app_login');
        }
        return $this->render('admin/product/index.html.twig', [
            'products' => $ProductRepo->findWithCategory()
        ]);
    }
}
