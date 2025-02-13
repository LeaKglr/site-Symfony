<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_dashboard')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/create', name: 'admin_product_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            // Déplacement du fichier
            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            // On sauvegarde le nom du fichier dans l'entité
            $product->setImage($newFilename);
        }

        // Ajout des stocks
        $sizes = ['xs', 's', 'm', 'l', 'xl'];
        foreach ($sizes as $size) {
            $stockQuantity = $form->get("stock_$size")->getData();
            if ($stockQuantity !== null) {
                $stock = new Stock();
                $stock->setSize(strtoupper($size));
                $stock->setQuantity($stockQuantity);
                $stock->setProduct($product);
                $em->persist($stock);
            }
        }

        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    // ⚠️ C'est cette ligne qui est essentielle pour que `form` existe dans Twig
    return $this->render('admin/index.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/edit/{id}', name: 'admin_product_edit')]
    public function edit(ProductRepository $productRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Produit non trouvé");
        }
        
        // Mise à jour des valeurs de base
        $product->setName($request->request->get('name'));
        $product->setPrice($request->request->get('price'));
        
        // Mise à jour des stocks
        foreach ($product->getStocks() as $stock) {
            $sizeKey = 'stock_' . strtolower($stock->getSize()); // Ex: stock_xs
            if ($request->request->has($sizeKey)) {
                $stock->setQuantity((int) $request->request->get($sizeKey));
            }
        }
        
        $entityManager->persist($product);

        foreach ($product->getStocks() as $stock) {
            $entityManager->persist($stock);
        }

        $entityManager->getUnitOfWork()->computeChangeSets();
        $entityManager->flush();
        
        $this->addFlash('success', 'Produit mis à jour avec succès !');
        
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/delete/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('admin_dashboard');
    }
}

