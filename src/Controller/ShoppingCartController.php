<?php

namespace App\Controller;

use App\Entity\ShoppingCart;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shopping_cart', name: 'shopping_cart_')]
class ShoppingCartController extends AbstractController
{
    public function index() : Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $shoppingCart = $user->getShoppingCart();
        } else {
            $shoppingCart = new ShoppingCart();
        }
        return $this->render('shopping_cart/index.html.twig', [
            'title'=>'Mon panier',
            'shoppingCart' => $shoppingCart
        ]);
    }
}
