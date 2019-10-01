<?php

namespace App\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $shipping = 4.50;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(Product $product)
    {
        // le but est d'ajouter un produit au panier

        $cart = $this->session->get('cart-items', new Cart());

        if ($cart->contains($product->getId()) === false) {
            $cart->add($product, 1);
        } else {
            $cart->get($product->getId())->increment();
        }

        $this->session->set('cart-items', $cart);
    }

    public function remove(Product $product): bool
    {

        if (!$this->session->has('cart-items')) {
            return false;
        }

        $cart = $this->session->get('cart-items');

        $cart->remove($product->getId());

        $this->session->set("cart_items", $cart);

        return true;
    }

    public function empty(): bool
    {
        // le but est de vider le panier
        $this->session->remove('cart-items');
        return true;
    }

    public function getItems(): array
    {
        return $this->session->get('cart-items', new Cart())->all();
    }

    public function getTotal(): float
    {
        $cart = $this->session->get('cart-items', new Cart());
        return $cart->getTotal();
    }

    public function getShipping(): float
    {
        return $this->shipping;
    }

    public function getGrandTotal(): float
    {
        return $this->shipping + $this->getTotal();
    }
}
