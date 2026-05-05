<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ImageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $ProductRepo,
        private EntityManagerInterface $em,
    ) 
    {
    }

    #[Route('/admin/product', name: 'app_admin_product_index')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute($this->getUser() ? 'app_product_index' : 'app_login');
        }
        return $this->render('admin/product/index.html.twig', [
            'products' => $this->ProductRepo->findWithCategory()
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'app_admin_product_delete', requirements: ['id' => '\d+'], defaults: ['id' => null])]   
    public function delete(?Product $product, ImageHandler $imageHandler, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute($this->getUser() ? 'app_product_index' : 'app_login');
        }
        $submittedToken = $request->getPayload()->get('token');

        if ($product && $this->isCsrfTokenValid('delete-product'. $product->getId(), $submittedToken)) {
            // suppression des images : 
            $imageHandler->deleteImages($product);
            
            $this->em->remove($product);
            $this->em->flush();

            $this->addFlash('success', 'Le produit a été supprimé avec succès.');
            return $this->redirectToRoute('app_admin_product_index');
        }
        $this->addFlash('danger', 'Un problème est survenue.');

        return $this->redirectToRoute('app_admin_product_index');

    }

    #[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function add(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute($this->getUser() ? 'app_product_index' : 'app_login');
        }
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        return $this->render('admin/product/add.html.twig', [
           'form' => $form->createView(), 
        ]);
    }
}
