<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: 'index')]
    public function product_index(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $product = $entityManager
            ->getRepository(Product::class)
            ->findAll();
        return $this->render('product/index.html.twig', [
            'controller_name' => $product,
        ]);
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param Product $product
     * @param string $message
     * @param string $slug
     * @return Response
     */
    #[Route('/{slug}', name: 'detail')]
    public function product_detail(
        EntityManagerInterface $entityManager,
        Product $product,
        string $message,
        string $slug
    ): Response
    {
        try {
            $product = $entityManager
                ->getRepository(Product::class)
                ->findBySlug($slug);
        } catch (Exception $e) {
            $message = "Ce produit n'existe pas";
        }
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'message' => $message
        ]);
    }
}
