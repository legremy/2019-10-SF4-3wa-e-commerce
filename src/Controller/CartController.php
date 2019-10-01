<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{



    /**
     * @Route("/cart", name="cart_index")
     *
     * @return void
     */
    public function index(CartService $cartService)
    {

        $items = $cartService->getItems();
        $shipping = $cartService->getShipping();
        $total = $cartService->getTotal();
        $grandTotal = $cartService->getGrandTotal();

        return $this->render("cart/index.html.twig", [
            "items" => $items,
            'shipping' => $shipping,
            'total' => $total,
            'grandTotal' => $grandTotal
        ]);
    }


    // toutes les fonctions doivent parler avec la session

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     *
     * @param Product $product
     * @param SessionInterface $session
     * @return void
     */
    public function add(Product $product, CartService $cartService)
    {
        $cartService->add($product);
        $this->addFlash("success", "Le produit <strong>" . $product->getTitle() . "</strong> a été ajouté avec succès!");

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     *
     * @param Product $product
     * @param SessionInterface $session
     * @return void
     */
    public function remove(Product $product, CartService $cartService)
    {
        $cartService->remove($product);
        $this->addFlash("warning", "Le produit <strong>" . $product->getTitle() . "</strong> a été supprimé du panier.");
        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     *
     * @param SessionInterface $session
     * @return void
     */
    public function empty(CartService $cartService)
    {
        $cartService->empty();
        return $this->redirectToRoute("cart_index");
    }

    public function nbProducts(CartService $cartService)
    {
        $nbProducts = 0;
        foreach ($cartService->getItems() as $item) {
            $nbProducts += $item->getQuantity();
        }

        return $this->render(
            'partials/nbProducts.html.twig',
            ['nbProducts' => $nbProducts]
        );
    }
}
