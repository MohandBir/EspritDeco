<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    const OPEN = 'open';
    const CONVERTED = 'converted';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, CartLine>
     */
    #[ORM\OneToMany(targetEntity: CartLine::class, mappedBy: 'cart', orphanRemoval: true)]
    private Collection $cartLines;

    public function __construct()
    {
        $this->cartLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, CartLine>
     */
    public function getCartLines(): Collection
    {
        return $this->cartLines;
    }

    public function addCartLine(CartLine $cartLine): static
    {
        if (!$this->cartLines->contains($cartLine)) {
            $this->cartLines->add($cartLine);
            $cartLine->setCart($this);
        }

        return $this;
    }

    public function removeCartLine(CartLine $cartLine): static
    {
        if ($this->cartLines->removeElement($cartLine)) {
            // set the owning side to null (unless already changed)
            if ($cartLine->getCart() === $this) {
                $cartLine->setCart(null);
            }
        }

        return $this;
    }
}
