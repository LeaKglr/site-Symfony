<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $priceRange = $request->query->get('priceRange');

    $queryBuilder = $productRepository->createQueryBuilder('p');

    if ($priceRange === '10-29') {
        $queryBuilder->andWhere('p.price BETWEEN :min AND :max')
                     ->setParameter('min', 10)
                     ->setParameter('max', 29);
    } elseif ($priceRange === '29-35') {
        $queryBuilder->andWhere('p.price BETWEEN :min AND :max')
                     ->setParameter('min', 29)
                     ->setParameter('max', 35);
    } elseif ($priceRange === '35-50') {
        $queryBuilder->andWhere('p.price BETWEEN :min AND :max')
                     ->setParameter('min', 35)
                     ->setParameter('max', 50);
    }

    $products = $queryBuilder->getQuery()->getResult();

    return $this->render('product/index.html.twig', [
        'products' => $products,
        'currentRange' => $priceRange
    ]);
    }

    #[Route('/product/{id}', name: 'product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Liste des tailles disponibles
        $sizes = ['XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL'];

        // Création du formulaire
        $form = $this->createFormBuilder()
            ->add('size', ChoiceType::class, [
                'choices' => $sizes,
                'label' => 'Choisir une taille',
                'expanded' => false, // Menu déroulant
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter au panier'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la taille sélectionnée
            $selectedSize = $form->get('size')->getData();

            // Récupérer ou initialiser le panier en session
            $session = $request->getSession();
            $cart = $session->get('cart', []);

            // Ajouter le produit au panier
            $cart[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'size' => $selectedSize,
                'price' => $product->getPrice(),
                'image' => $product->getImage(),
            ];

            $session->set('cart', $cart);
            $session->save();

            // Redirection avec message de succès
            $this->addFlash('success', 'Produit ajouté au panier !');
            return $this->redirectToRoute('product_show', ['id' => $id]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
