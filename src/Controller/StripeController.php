<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

final class StripeController extends AbstractController
{
    #[Route('/checkout', name: 'stripe_checkout')]
    public function checkout(SessionInterface $session): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return $this->redirectToRoute('cart_show');
        }

        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => $item['name']],
                    'unit_amount' => $item['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], true),
            'cancel_url' => $this->generateUrl('cart_show', [], true),
        ]);

        return new JsonResponse(['id' => $checkoutSession->id]);
    }

    #[Route('/checkout/success', name: 'stripe_success')]
    public function success(SessionInterface $session): Response
    {
        $session->remove('cart'); // Vider le panier après paiement réussi

        return $this->render('cart/success.html.twig');
    }
}
