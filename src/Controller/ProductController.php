<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/create", name="product_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product); // On persiste l'objet
            $entityManager->flush(); // On exécute la requête (INSERT...)
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

    /**
     * @Route("/product", name="product_index")
     */
    public function index(ProductRepository $repository)
    {
        // $this->getDoctrine()->getRepository(Product::class)->findAll();
        $products = $repository->findAll();

        dump($products);

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show(Product $product, $id, ProductRepository $repository)
    {
        //$product = $repository->find($id);

        //if (!$product) {
        //    throw $this->createNotFoundException();
        //}

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On peut récupérer les infos directement dans la request
            $product->setName($request->request->get('product')['name']);

            $entityManager->flush(); // On exécute la requête (UPDATE...)
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager)
    {
        $token = $request->request->get('csrf_token');

        // Ici, on se protège d'une faille CSRF
        if ($this->isCsrfTokenValid('delete-'.$product->getId(), $token)) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
