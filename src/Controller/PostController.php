<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{
    #[Route(path:"/", name: "app_post")]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser(); // Pas besoin d'injecter Security directement
        $products = $productRepository->findBy(['id' => [1, 4, 9]]);

        return $this->render('post/index.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

}
