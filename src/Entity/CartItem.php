<?php

namespace App\Entity;

class CartItem
{
    /**
     * product respresenting the item
     *
     * @var Product
     */
    protected $product;

    /**
     * quantity of products
     *
     * @var int
     */
    protected $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * Get the value of product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Set the value of product
     *
     * @return  self
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of quantity
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function increment(): self
    {
        $this->quantity++;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->quantity * $this->product->getPrice();
    }
}
