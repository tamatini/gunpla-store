<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Product;
use App\Form\SearchType;
use App\Service\CartItemService;
use App\Service\ShoppingCartService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop', name: 'product_')]
class ProductController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function productIndex(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $data = new SearchData();
        $searchForm = $this->createForm(SearchType::class, $data);
        $searchForm->handleRequest($request);
        [$min, $max] = $entityManager->getRepository(Product::class)->findMinMax($data);
        $products = $entityManager->getRepository(Product::class)->findSearch($data);
        return $this->render('product/index.html.twig', [
            'title' => "Tous les produits",
            'products' => $products,
            'searchForm' => $searchForm->createView(),
            'min'=>$min,
            'max'=>$max
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return Response return the product detail page
     */
    #[Route('/{id}', name: 'detail')]
    public function getProduct(
        EntityManagerInterface $entityManager,
        int                    $id
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            throw $this->createNotFoundException('Ce produit n\'existe pas');
        }
        return $this->render('product/detail.html.twig', [
            'title' => $product->getReference(),
            'product' => $product
        ]);
    }

    #[Route('/add_to_cart/{slug}', name: 'add_to_cart')]
    public function addToCart(
        ShoppingCartService    $shoppingCartService,
        CartItemService        $cartItemService,
        EntityManagerInterface $entityManager,
        string                 $slug,
        Request                $request
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)->findBySlug($slug);
        $shoppingCart = $shoppingCartService->getCurrentShoppingCart();

        $cartItem = $cartItemService->getCartItem($shoppingCart, $product);
        $shoppingCart->addCartItem($cartItem);
        $shoppingCartService->saveShoppingCart($shoppingCart, $cartItem);
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
