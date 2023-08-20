<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/detail/{slug}', name: 'detail')]
    public function product_detail(
        EntityManagerInterface $entityManager,
        Product $product,
        string $message,
        string $slug
    ): Response
    {
        try {
            $product = $entityManager
                -> getRepository(Product::class)
                -> findBySlug($slug);
        } catch (Exception $e) {
            $message = "Ce produit n'existe pas";
        }
        return $this -> render('product/detail.html.twig', [
            'product' => $product,
            'message' => $message
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/new', name:'new')]
    public function new_product(
        EntityManagerInterface $entityManager,
        Request $request
    ) : Response
    {
        $product = new Product();
        $productForm = $this -> createForm(ProductType::class, $product);
        $productForm -> handleRequest($request);
        if (
            $productForm    -> isSubmitted() &&
            $productForm    -> isValid()
        ) {
            $entityManager  -> persist($product);
            $entityManager  -> flush();
            $this           -> addFlash("success", "L'article à bien été rajouter");
            return $this    -> redirectToRoute('product_new');
        }
        return $this->render('product/new.html.twig', [
            'title'         => 'Ajouter un nouveau produit',
            'product_form'  => $productForm->createView()
        ]);
    }
}
