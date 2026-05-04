<?php

namespace App\Controller;

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

    #[Route('/admin/product/show/{id}', name: 'app_admin_product_show', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function show(?int $id, ProductRepository $productRepo): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute($this->getUser() ? 'app_product_index' : 'app_login');
        }
        $product = $productRepo->findOneWithCategoryAndImage((int) $id);

        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
