<?php

namespace App\Controller;

use App\Entity\ShopOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends AbstractController
{
    #[Route('/payment/create/{orderId}', name: 'payment_create', methods: ['POST'])]
    public function createPayment(int $orderId, EntityManagerInterface $em): Response
    {
        $order = $em->getRepository(ShopOrder::class)->find($orderId);

        if (!$order) {
            return $this->json(['error' => 'Commande introuvable'], 404);
        }

        // 🔐 Sécurité : seul le propriétaire peut payer
        if ($order->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $intent = PaymentIntent::create([
            'amount' => (int) ($order->getTotal() * 100),
            'currency' => 'eur',
            'metadata' => [
                'order_id' => $order->getId(),
            ],
        ]);

        return $this->json([
            'client_secret' => $intent->client_secret,
        ]);
    }

    #[Route('/payment/success/{orderId}', name: 'payment_success')]
    public function paymentSuccess(int $orderId, EntityManagerInterface $em): Response
    {
        $order = $em->getRepository(ShopOrder::class)->find($orderId);

        if (!$order) {
            throw $this->createNotFoundException();
        }

        // 🔐 sécurité
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // ✅ Paiement simulé réussi
        if ($order->getStatus() !== 'PAID') {
            $order->setStatus('PAID');
            $em->flush();
        }

        return $this->render('payment/success.html.twig', [
            'order' => $order,
            'redirect_after' => '/shop/merch'
        ]);
    }
}
