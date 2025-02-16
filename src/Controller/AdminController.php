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

        // On créé un objet vide que l'on veut enregistrer
        $product = new Product();
        // On génère le formulaire auquel on rattache la catégorie
        $form = $this->createForm(ProductType::class, $product);

        // On rattache le composant HttpFoudation\Request au formulaire pour qu'il puisse récupérer les données
        // une fois le formulaire soumis
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis et que les données sont valides
        // avant de continuer l'enregistrement
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                $product->setImage($newFilename);
            }
        
            // Persiste le produit
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
        
            // Flush une seule fois
            $em->flush();
        
            return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }
        


        $products = $productRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'products' => $products,
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

