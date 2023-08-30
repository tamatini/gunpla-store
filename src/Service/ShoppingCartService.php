<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\ShoppingCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ShoppingCartService
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    const COOKIE_NAME = 'cart_id';

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    )
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function getShoppingCart() : ? ShoppingCart
    {
        return $this->entityManager->getRepository(ShoppingCart::class)->findOneBy(['id'=>$this->getShoppingCartId()]);
    }

    public function getCurrentShoppingCart() : ?ShoppingCart
    {
        $shoppingCart = $this->getShoppingCart();
        if ($shoppingCart === null) {
            $shoppingCart = new ShoppingCart();
        }
        return $shoppingCart;
    }

    public function setShoppingCart(ShoppingCart $shoppingCart) : void
    {
        $this->getSession()->set(self::COOKIE_NAME, $shoppingCart->getId());
    }

    private function getShoppingCartId(): ? int
    {
        return $this->getSession()->get(self::COOKIE_NAME);
    }

    private function getSession() : SessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function saveShoppingCart(ShoppingCart $shoppingCart, CartItem $cartItem) : void
    {
        $this->entityManager->persist($cartItem);
        $this->entityManager->persist($shoppingCart);
        $this->entityManager->flush();
        $this->setShoppingCart($shoppingCart);
    }

}