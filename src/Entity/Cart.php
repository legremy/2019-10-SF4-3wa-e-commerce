<?php

namespace App\Entity;

class Cart
{
    protected $items = [];

    public function add(Product $product, int $quantity)
    {
        $this->items[$product->getId()] = new CartItem($product, $quantity);
        return $this;
    }


    /**
     * Retire un produit du panier grâce à son identifiant
     *
     * @param integer $productId
     * @return self
     */
    public function remove(int $productId): self
    {
        unset($this->items[$productId]);
        return $this;
    }

    public function get(int $productId): ?CartItem
    {
        if (empty($this->items[$productId])) {
            return null;
        }
        return $this->items[$productId];
    }

    public function contains(int $productId): bool
    {
        return !empty($this->items[$productId]);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getTotal(): float
    {
        return array_reduce($this->items, function (float $total, CartItem $item) {
            return $total += $item->getTotal();
        }, 0);
    }
}
