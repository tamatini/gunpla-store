<?php

namespace App\Tests\Entity;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use App\Repository\ShoppingCartRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    private CartItem $cartItem;

    protected function setUp(): void
    {
        // Create product
        $product = new Product();
        $product -> setReference("re-zel");
        $product -> setSlug("re-zel");
        $product -> setDescription("Retrouver le Re-zel de la série Mobile Suit Gundam UC et revivez les meilleurs moment \n
        - Beam Rifle \n
        - Beam Saber x2 \n
        - Shield \n
        - Sticker \n
        Kit transformable. 
        ");
        $product -> setWeight(810);
        $product -> setHeight(310);
        $product -> setWidth(39);
        $product -> setLength(820);
        $product -> setScale("1/100");
        $product -> setSellPrice(36.14);
        $product -> setStock(20);

        // Create user
        $user = new User();
        $user->setUsername("johnDoe");
        $user->setPassword("password");

        // Create shopping cart
        $shoppingCart = new ShoppingCart();
        $shoppingCart->setUser($user);


        // Add item to cart
        $cartItem = $this->cartItem = new CartItem();
        $cartItem->addProduct($product);
        $cartItem->setQuantity(3);

        $this->cartItemRepository = $this->createMock(CartItemRepository::class);
        $this->cartItemRepository->expects($this->any())
            ->method('find')
            ->willReturn($cartItem);
    }

    public function testAddCartItem() : void
    {
        $cart = $this->cartItemRepository->find('id', $this->cartItem->getId());
        $this->assertEquals($this->cartItem, $cart);
        $this->assertEquals($this->cartItem->getProduct(), $cart->getProduct());
        $this->assertEquals(3, $cart->getQuantity());
    }
}