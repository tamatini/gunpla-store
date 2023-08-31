<?php

namespace App\Controller;

use App\Entity\ShoppingCart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shopping_cart', name: 'shopping_cart_')]
class ShoppingCartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager) : Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $shoppingCart = $entityManager->getRepository(ShoppingCart::class)->findOneBy(['user'=>$user]);
        } else {
            $shoppingCart = new ShoppingCart();
        }
        return $this->render('shopping_cart/index.html.twig', [
            'title'=>'Mon panier',
            'shoppingCart' => $shoppingCart
        ]);
    }
}
