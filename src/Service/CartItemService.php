<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use Doctrine\ORM\EntityManagerInterface;

class CartItemService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCartItem(ShoppingCart $shoppingCart, Product $product) : ? CartItem
    {
        $cartItem = $this->entityManager->getRepository(CartItem::class)
            ->findOneBy([
                'shoppingCart'=> $shoppingCart,
                'product'=>$product
            ]);

        if (!$cartItem) {
            $cartItem = new CartItem();
            $cartItem->setShoppingCart($shoppingCart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity()+1);
            $cartItem->setUpdatedAt(new \DateTimeImmutable());
        }

        return $cartItem;
    }

}