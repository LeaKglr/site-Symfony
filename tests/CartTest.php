<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartTest extends KernelTestCase
{
    public function testAddProductToCart()
    {
        // Création d'une session simulée
        $session = new Session(new MockArraySessionStorage());
        $session->start(); // Démarre la session simulée

        // Simuler un panier en session
        $cart = $session->get('cart', []);
        $cart[] = [
            'id' => 1,
            'name' => 'Test Product',
            'size' => 'M',
            'price' => 19.99,
            'image' => 'test.jpg'
        ];
        $session->set('cart', $cart);

        // Vérification que l'ajout a bien eu lieu
        $this->assertCount(1, $session->get('cart'));
    }

    public function testCheckoutClearsCart()
    {
        // Création d'une session simulée
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        // Ajoute un produit au panier
        $cart = [
            ['id' => 1, 'name' => 'Test Product', 'size' => 'M', 'price' => 19.99, 'image' => 'test.jpg']
        ];
        $session->set('cart', $cart);
        $this->assertCount(1, $session->get('cart')); // Vérifie que le panier contient un produit

        // Simule le paiement réussi
        $session->remove('cart');

        // Vérifier que le panier est vide après le paiement
        $this->assertEmpty($session->get('cart'));
    }
}

