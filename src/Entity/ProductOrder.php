<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductOrderRepository")
 */
class ProductOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productOrders")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="productOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cde;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCde(): ?Order
    {
        return $this->cde;
    }

    public function setCde(?Order $cde): self
    {
        $this->cde = $cde;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
