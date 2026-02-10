<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $q = (string) $request->query->get('q', '');
        $sort = (string) $request->query->get('sort', 'id');
        $dir = (string) $request->query->get('dir', 'desc');

        $players = $userRepository->findPlayersAndCaptains($q, $sort, $dir);

        return $this->render('player/index.html.twig', [
            'players' => $players,
            'q' => $q,
            'sort' => $sort,
            'dir' => strtolower($dir),
        ]);
    }
}
