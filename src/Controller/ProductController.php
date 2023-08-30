<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Entity\User;
use App\Form\ProductType;
use App\Service\CartItemService;
use App\Service\ShoppingCartService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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
    public function productIndex(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            'title' => "Tous les produits",
            'products' => $products,
        ]);
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param string $message
     * @param string $slug
     * @return Response return the product detail page
     * @throws NonUniqueResultException
     */
    #[Route('/{slug}', name: 'detail')]
    public function getProduct(
        EntityManagerInterface $entityManager,
        string $message,
        string $slug
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)-> findBySlug($slug);
        return $this -> render('product/detail.html.twig', [
            'title' => $product->getReference(),
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
    public function newProduct(
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
     * @throws NonUniqueResultException
     */
    #[Route('/{slug}/update', name:'update')]
    public function updateProduct(
        EntityManagerInterface $entityManager,
        Request $request,
        string $slug
    ) : Response {
        $product = $entityManager->getRepository(Product::class)->findBySlug($slug);
        if (!$product) {
            throw $this->createNotFoundException(
                'Ce produit n\'existe pas'
            );
        }
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit à bien été mis à jour');
            $this->redirectToRoute('product_detail', ['slug' => $slug]);
        }
        return $this->render('product/new.html.twig', [
            'title'=>'Mettre à jour le produit',
            'productForm'=>$productForm
        ]);
    }

    #[Route('/{slug}/delete', name: 'delete')]
    public function deleteProduct(
        EntityManagerInterface $entityManager,
        string $slug
    ) : Response {
        $product = $entityManager->getRepository(Product::class)->findBySlug($slug);
        if (!$product) {
            throw $this->createNotFoundException('Ce produit n\'existe pas');
        }
        $entityManager->remove($product);
        $entityManager->flush();
        $this->addFlash('success', 'Le produit à bien été supprimé');
        return $this->redirectToRoute('product_index');
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $slug
     * @param Request $request
     * @return Response add product to shopping cart and return to the current position
     * @throws NonUniqueResultException
     */
    /*#[Route('/add_to_cart/{slug}', name: 'add_to_cart')]
    public function add_to_cart(
        EntityManagerInterface $entityManager,
        string $slug,
        Request $request
    ) : Response {
        /** @var User $user
        $user = $this->getUser();

        $product = $entityManager -> getRepository(Product::class) -> findBySlug($slug);
        $userShoppingCart = $user->getShoppingCart();
        $currentPosition = $this -> redirect($request->getUri());

        if ($product->getStock() === 0) {
            $this->addFlash(
                "error",
                $product->getReference() . " n'est plus en stock"
            );
            return $currentPosition;
        }

        // Create new cart if cart does not exist
        if ($userShoppingCart === null) {
            $userShoppingCart = $user->setShoppingCart(new ShoppingCart());
            $entityManager->persist($userShoppingCart);
            $entityManager->flush();
        }

        // Get shopping cart
        $shoppingCart = $entityManager->getRepository(ShoppingCart::class)
            ->findById($userShoppingCart->getId());

        // Check if product still in stock
        if ($product->getStock() < $cartItem->getQuantity()) {
            $this -> addFlash(
                "error",
                "Nous n'avons pas assez de stock sur ce produit - 
                Quantité disponible : " . $product->getStock() . "pièce(s)"
            );
            return $currentPosition;
        }

        $currentItem = $entityManager->getRepository(CartItem::class)
            ->findOneBy(['shoppingCart' => $shoppingCart, 'product' => $product]);

        if ($currentItem != null ) {
            $currentItem
                ->setQuantity($currentItem->getQuantity()+1)
                ->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($currentItem);
        } else {
            $cartItem
                -> setProduct($product)
                -> setQuantity(1);
            $shoppingCart->addCartItem($cartItem);
            $entityManager->persist($cartItem);
        }

        $this -> addFlash(
            "success",
            "Le produit " . $product->getReference() . "à bien été ajouter au panier"
        );

        $entityManager->persist($shoppingCart);
        $entityManager->flush();
        return $this->redirectToRoute('home_index');
    }*/

    #[Route('/add_to_cart/{slug}', name: 'add_to_cart')]
    public function addToCart(ShoppingCartService $shoppingCartService, CartItemService $cartItemService,
        EntityManagerInterface $entityManager, string $slug, Request $request): Response
    {
        $product = $entityManager->getRepository(Product::class)->findBySlug($slug);
        $shoppingCart = $shoppingCartService->getCurrentShoppingCart();

        $cartItem = $cartItemService->getCartItem($shoppingCart, $product);
        $shoppingCart->addCartItem($cartItem);
        $shoppingCartService->saveShoppingCart($shoppingCart, $cartItem);
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
