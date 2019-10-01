<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductsType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Utils\Markdown;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products_index")
     */
    public function index(ProductRepository $productsRepo, CategoryRepository $categoriesRepo)
    {
        $products = $productsRepo->findAll();
        $categories = $categoriesRepo->findAll();
        return $this->render('products/index.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/products/new", name="products_create")
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $product = new Product();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('products_index');
        }
        return $this->render('products/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/products/{id}", name="products_show")
     */
    public function show(Product $product, Markdown $parser, CacheInterface $cache)
    {
        $cachedDescription = $cache->get(md5($product->getIntroduction()), function (ItemInterface $item) use ($parser, $product) {
            return $toto = $parser->toHtml($product->getDescription());
        });

        $product->setDescription($cachedDescription);

        return $this->render('products/show.html.twig', [
            'product' => $product
        ]);
    }



    /**
     * @Route("/products/update/{id}", name="products_update")
     */
    public function update(Product $product, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('products_index');
        }
        return $this->render('products/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/products/delete/{id}", name="products_delete")
     */
    public function delete(Product $product, ObjectManager $manager)
    {
        $manager->remove($product);
        return $this->redirectToRoute("products_index");
    }

    /**
     * @Route("/search", name="product_search")
     *
     * @return void
     */
    public function search(Request $request, ProductRepository $repo)
    {
        $search = $request->query->get("search", null);
        $results = [];
        if ($search) {
            $results = $repo->findBySearch($search);
        }
        return $this->render('products/search.html.twig', [
            'results' => $results,
            'search' => $search
        ]);
    }
}
