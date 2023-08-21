<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $product = $this->product = new Product();
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

        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productRepository->expects($this->any())
            ->method('findBySlug')
            ->willReturn($product);
    }

    /**
     * Test product creation
     * @return void
     */
    public function testCreateNewProduct() : void
    {
        $product = $this->productRepository
            ->findBySlug($this->product->getSlug());

        $this->assertEquals($this->product, $product);
        $this->assertEquals("re-zel", $product->getReference());
        $this->assertEquals("re-zel", $product->getSlug());
        $this->assertEquals(810, $product->getWeight());
        $this->assertEquals(310, $product->getHeight());
        $this->assertEquals(39, $product->getWidth());
        $this->assertEquals(820, $product->getLength());
        $this->assertEquals("1/100", $product->getScale());
        $this->assertEquals(36.14, $product->getSellPrice());
        $this->assertEquals(20, $product->getStock());
    }
}
