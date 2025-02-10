<?php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


final class StripeController extends AbstractController
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    #[Route('/checkout', name: 'stripe_checkout')]
    public function checkout(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return $this->redirectToRoute('cart_show');
        }

        $checkoutSession = $this->stripeService->createCheckoutSession($cart);
        
        return new Response(
            '<meta http-equiv="refresh" content="0;url=' . $checkoutSession->url . '">' .
            '<p>Si vous n\'êtes pas redirigé, <a href="' . $checkoutSession->url . '">cliquez ici</a>.</p>'
        );
        
    }

    #[Route('/checkout/success', name: 'stripe_success')]
    public function success(SessionInterface $session): Response
    {
        $session->remove('cart'); // Vider le panier après paiement réussi
        return $this->render('cart/succes.html.twig');
    }
}