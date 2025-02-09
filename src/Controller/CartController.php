<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show')]
    public function show(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        // Calculer le total du panier
        $total = array_reduce($cart, function ($sum, $item) {
            return $sum + $item['price'];
        }, 0);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/remove/{index}', name: 'cart_remove')]
    public function remove(int $index, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $session->set('cart', array_values($cart)); // Réindexe le tableau
        }

        return $this->redirectToRoute('cart_show');
    }
}
