<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class PostController extends AbstractController
{
    // Page principale de l'application
    #[Route(path:"/", name: "app_post")]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $products = $productRepository->findBy(['id' => [1, 4, 9]]); // Récupération des 3 produits à mettre en avant

        return $this->render('post/index.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

    #[Route('/docs', name: 'app_docs')]
    public function docs(): Response
    {
        return $this->render('docs/index.html.twig');
    }

}
