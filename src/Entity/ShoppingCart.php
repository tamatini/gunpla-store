<?php

namespace App\Entity;

use App\Repository\ShoppingCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingCartRepository::class)]
class ShoppingCart
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'shoppingCart', targetEntity: CartItem::class, orphanRemoval: true)]
    private Collection $cartItem;

    #[ORM\OneToOne(inversedBy: 'shoppingCart', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(options: ["default"=>0.00])]
    private ?float $total = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->cartItem = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->setTotal(0.00);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItem(): Collection
    {
        return $this->cartItem;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        /*foreach ($this->getCartItem() as $currentCartItem){
            if ($currentCartItem->equals($cartItem)) {
                $currentCartItem->setQuantity(
                    $currentCartItem->getQuantity() + $cartItem->getQuantity()
                );
            }
        }*/
        if (!$this->cartItem->contains($cartItem)) {
            $this->cartItem->add($cartItem);
            $cartItem->setShoppingCart($this);
        }
        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItem->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getShoppingCart() === $this) {
                $cartItem->setShoppingCart(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotal(): ?float
    {
        $total = 0;
        foreach ($this->getCartItem() as $item) {
            $total += ($item->getQuantity() * $item->getProduct()->getSellPrice());
        }
        return $total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
