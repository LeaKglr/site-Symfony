<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartTest extends WebTestCase
{
    public function testAddProductToCart()
    {
        $client = static::createClient();
        
        // Accéder à un produit spécifique (ex: produit ID 1)
        $crawler = $client->request('GET', '/product/1');
        $this->assertResponseIsSuccessful();

        // Sélection de la taille et ajout au panier
        $form = $crawler->selectButton('Ajouter au panier')->form([
            'cart[size]' => 'M'
        ]);
        $client->submit($form);

        // Vérifier que le produit est bien ajouté
        $client->request('GET', '/cart');
        $this->assertSelectorTextContains('.cart-item', 'Produit 1');
        $this->assertSelectorTextContains('.cart-item-size', 'M');

        echo "✅ Produit ajouté au panier avec taille sélectionnée.\n";
    }
}
