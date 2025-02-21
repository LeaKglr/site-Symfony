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
use App\Entity\Stock;

// Backoffice
#[Route('/admin')]
class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_dashboard')]
public function index(ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    set_time_limit(300);

    // Création d'un nouveau produit
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
            $product->setImage($newFilename);
        } else {
            $product->setImage('default.jpg');
        }

        $em->persist($product);

        // Ajout des stocks
        $sizes = ['xs', 's', 'm', 'l', 'xl'];
        foreach ($sizes as $size) {
            $stockQuantity = $form->get("stock_$size")->getData();
            if (!empty($size) && is_string($size)) {
                $stock = new Stock();
                $stock->setSize(strtoupper($size));
                $stock->setQuantity((int) $stockQuantity);
                $product->addStock($stock);
                $em->persist($stock);
            }
        }

        $em->flush();
        return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    // Récupération des produits pour l'affichage
    $products = $productRepository->findAll();
    $editForms = [];

    foreach ($products as $product) {
        // Création du formulaire d'édition
        $editForm = $this->createForm(ProductType::class, $product, [
            'is_edit' => true, // Empêche l'affichage de l'input image
        ]);

        // Remplir les champs de stock avec les valeurs actuelles
        foreach ($product->getStocks() as $stock) {
            $sizeKey = 'stock_' . strtolower($stock->getSize()); // stock_xs, stock_s, etc.
            
            if ($editForm->has($sizeKey)) {  // Vérifie que le champ existe bien dans le formulaire
                $editForm->get($sizeKey)->setData($stock->getQuantity());
            }
        }

        $editForms[$product->getId()] = $editForm->createView();
    }

    return $this->render('admin/index.html.twig', [
        'products' => $products,
        'product' => $product,
        'form' => $form->createView(),
        'editForms' => $editForms,
    ]);
}

#[Route('/edit/{id}', name: 'admin_product_edit')]
public function edit(ProductRepository $productRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    $product = $productRepository->find($id);

    if (!$product) {
        throw $this->createNotFoundException("Produit non trouvé");
    }

    // Création du formulaire
    $editForm = $this->createForm(ProductType::class, $product);

    // Remplir les champs de stock avec les valeurs actuelles
    foreach ($product->getStocks() as $stock) {
        $sizeKey = 'stock_' . strtolower($stock->getSize()); // stock_xs, stock_s, etc.
        
        if ($editForm->has($sizeKey)) {  // Vérifie que le champ existe bien dans le formulaire
            $editForm->get($sizeKey)->setData($stock->getQuantity());
        }
    }

    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
        $product->setName($editForm->get('name')->getData());
        $product->setPrice($editForm->get('price')->getData());

        // Mise à jour des stocks (ajout/modification)
        $sizes = ['xs', 's', 'm', 'l', 'xl'];
        foreach ($sizes as $size) {
            $sizeKey = 'stock_' . $size;
            $newQuantity = (int) $editForm->get($sizeKey)->getData();
        
            // Vérifie si le stock existe déjà
            $existingStock = null;
            foreach ($product->getStocks() as $stock) {
                if (strtolower($stock->getSize()) === $size) {
                    $existingStock = $stock;
                    break;
                }
            }
        
            // Mise à jour ou création du stock
            if ($existingStock) {
                $existingStock->setQuantity($newQuantity);
            } else {
                $newStock = new Stock();
                $newStock->setSize(strtoupper($size));
                $newStock->setQuantity($newQuantity);
                $product->addStock($newStock);
                $entityManager->persist($newStock);
            }
        }

        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit mis à jour avec succès !');

        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('admin/edit.html.twig', [
        'editForm' => $editForm->createView(),
    ]);
}

    #[Route('/delete/{id}', name: 'admin_product_delete', methods: ['GET'])]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('admin_dashboard');
    }
}

