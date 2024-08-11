<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use function Monolog\getName;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'create_product')]
    public function createProduct(EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $product->setName('Mouse');
        $product->setPrice(40);
        $product->setDescription('Good Mouse here!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new Response('Check out this great product: '.$product->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }

    #[Route('/productviarepo/{id}', name: 'product_show_rep')]
    public function showViaRep(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository
            ->find($id);


        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new Response('Check out this great product: '.$product->getName());
    }

    #[Route('/productviareponame/{name}', name: 'product_show_name')]
    public function showViaName(ProductRepository $productRepository, string $name): Response
    {
        $products = $productRepository
            ->findByName($name);


        if (!$products) {
            throw $this->createNotFoundException(
                'No product found for id '.$name
            );
        }

        return new Response('Check out this great product: ' . dd($products));
    }

    #[Route('/productall/', name: 'product_all_fetch')]
    public function showFetchAll(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();


        if (!$products) {
            throw $this->createNotFoundException(
                'No product found'
            );
        }

        return new Response('Check out this great product: ' . dd($products));
    }

    #[Route('/productRel', name: 'productrel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $category->setName('Computer Peripherals');

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setDescription('Ergonomic and stylish!');

        // relates this product to the category
        $product->setCategory($category);

        $entityManager->persist($category);
        $entityManager->persist($product);
        $entityManager->flush();

        return new Response(
            'Saved new product with id: '.$product->getId()
            .' and new category with id: '.$category->getId()
        );
    }

    #[Route('/showrel/{id}', name: 'showrel')]
    public function showRel(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->find($id);
        $categoryName = $product->getCategory()->getName();

        return new Response(
            $product->getName()
            .' of category '.$categoryName
        );
    }

    #[Route('/showprodfromcat/{id}', name: 'showprodfromcat')]
    public function showProducts(CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);

        $products = $category->getProducts();
        $prodnames = "";

        foreach($products as $product)
        {
            $prodnames .= $product->getName() . "\n";
        }

        return new Response(
       //    "From catégory ".$category->getName().", we have:\n". $prodnames
        //dd($products)
        $prodnames
        );
    }
}
