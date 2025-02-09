<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path:"/", name: "app_post")]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->security->getUser();
        $products = $productRepository->findBy([], null, 3);

        return $this->render('post/index.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }
}
