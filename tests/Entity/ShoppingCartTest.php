<?php

namespace App\Tests\Entity;


use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class ShoppingCartTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $user = $this->user = new User();
        $user->setUsername("johnDoe");
        $user->setPassword("password");

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository->expects($this->any())
            ->method('find')
            ->willReturn($user);
    }

    public function testEmptyCart() : void
    {
        $cart = $this->userRepository->find('id', $this->user->getId());
        $this->assertEquals($this->user->getShoppingCart(), $cart->getShoppingCart());
        $this->assertEquals($this->user->getShoppingCart()->getCartItem(), $cart->getShoppingCart()->getCartItem());
    }
}
