<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
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
        $product = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            'controller_name' => $product,
        ]);
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param string $message
     * @param string $slug
     * @return Response return the product detail page
     */
    #[Route('/detail/{slug}', name: 'detail')]
    public function product_detail(
        EntityManagerInterface $entityManager,
        string $message,
        string $slug
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)-> findBySlug($slug);
        return $this -> render('product/detail.html.twig', [
            'product' => $product,
            'message' => $message
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response create a view of the product form
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
        if ($productForm-> isSubmitted() && $productForm->isValid()
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

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $slug
     * @param Request $request
     * @return Response add product to shopping cart and return to the current position
     * @throws Exception
     */
    #[Route('/add_to_cart/{slug}')]
    public function add_to_cart(
        EntityManagerInterface $entityManager,
        string $slug,
        Request $request
    ) : Response {
        $product = $entityManager -> getRepository(Product::class) -> findBySlug($slug);
        $cartItem = new CartItem();
        $currentPosition = $this -> redirect($request->getUri());

        if ($product->getStock() === 0) {
            $this->addFlash(
                "error",
                $product->getReference() . " n'est plus en stock"
            );
            return $currentPosition;
        }

        /** @var ShoppingCart $shoppingCart */
        $shoppingCart = $this -> getUser() -> getShoppingCart();

        // Adding product to cartItem
        $cartItem -> addProduct($product);
        $cartItem -> setQuantity(1);

        if ($product->getStock() < $cartItem->getQuantity()) {
            $this -> addFlash(
                "error",
                "Nous n'avons pas assez de stock sur ce produit - 
                Quantité disponible : " . $product->getStock() . "pièce(s)"
            );
            return $currentPosition;
        }

        // Adding cartItem to shoppingCart
        $shoppingCart -> addCartItem($cartItem);
        $this -> addFlash(
            "success",
            "Le produit " . $product->getReference() . "à bien été ajouter au panier"
        );
        return $currentPosition;
    }
}
