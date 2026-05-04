<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findWithCategoryAndImage();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);

        }
    #[Route('/show/{id}', name: 'app_product_show', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function show(?int $id, ProductRepository $productRepo): Response
    {
        $product = $productRepo->findOneWithCategoryAndImage((int) $id);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
