<?php
namespace App\Controller;

use App\Entity\Game; // Assure-toi que tu as une entité Game
use App\Entity\ShopProduct;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/shop/{type}', name: 'shop')]
    public function index(Request $request, EntityManagerInterface $em, string $type): Response
    {
        // récupère le paramètre de tri, default = ASC
        $order = $request->query->get('order', 'asc');

        // récupère le paramètre de jeu sélectionné (optionnel)
        $game = $request->query->get('game'); // null si aucun

        // récupère tous les jeux pour le select
        $games = $em->getRepository(Game::class)->findAll();

        // récupération des produits selon type + filtre jeu
        $criteria = ['type' => $type];
        if ($game) {
            $criteria['game'] = $game; // filtre par jeu
        }

        $products = $em->getRepository(ShopProduct::class)
            ->findBy(
                $criteria,
                ['price' => $order === 'desc' ? 'DESC' : 'ASC']
            );

        return $this->render('shop/index.html.twig', [
            'type' => strtoupper($type),
            'products' => $products,
            'order' => $order,
            'games' => $games,
            'game' => $game,
            
        ]);
    }
    
}
