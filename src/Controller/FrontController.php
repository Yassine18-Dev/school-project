<?php

namespace App\Controller;

use App\Repository\JeuRepository;
use App\Repository\PredictionRepository;
use App\Entity\Tournoi;
use App\Repository\TournoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front_home')]
    #[Route('/front', name: 'app_front_root')]
    public function index(JeuRepository $jeuRepository): Response
    {
        // On affiche quelques jeux en vedette sur la page d'accueil
        return $this->render('front/home.html.twig', [
            'featured_games' => $jeuRepository->findBy([], ['id' => 'DESC'], 3)
        ]);
    }

    #[Route('/games', name: 'app_front_games')]
    public function games(JeuRepository $jeuRepository): Response
    {
        return $this->render('front/games.html.twig', [
            'games' => $jeuRepository->findAll()
        ]);
    }

    #[Route('/tournaments', name: 'app_front_tournaments')]
    public function tournaments(Request $request, TournoiRepository $tournoiRepository, JeuRepository $jeuRepository): Response
    {
        $search = $request->query->get('q', '');
        $jeuId = $request->query->getInt('jeu', 0) ?: null;
        $sort = $request->query->get('sort', 'ASC');

        return $this->render('front/tournaments.html.twig', [
            'tournaments' => $tournoiRepository->searchAndFilter($search, $jeuId, $sort),
            'jeux' => $jeuRepository->findAll(),
            'search' => $search,
            'selectedJeu' => $jeuId,
            'sort' => $sort,
        ]);
    }

    #[Route('/predictions', name: 'app_front_predictions')]
    public function predictions(PredictionRepository $predictionRepository): Response
    {
        return $this->render('front/predictions.html.twig', [
            'predictions' => $predictionRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

    #[Route('/tournament/{id}', name: 'app_front_tournament_show')]
    public function showTournament(Tournoi $tournoi): Response
    {
        return $this->render('front/tournament_show.html.twig', [
            'tournoi' => $tournoi
        ]);
    }

    #[Route('/tournament/{id}/join', name: 'app_front_tournament_join')]
    public function joinTournament(Tournoi $tournoi): Response
    {
        if ($tournoi->getDateDebut() < new \DateTime()) {
            $this->addFlash('error', 'Les inscriptions sont closes pour ce tournoi car il a déjà commencé.');
            return $this->redirectToRoute('app_front_tournament_show', ['id' => $tournoi->getId()]);
        }

        // Ici, on ajouterait la logique d'inscription (vérifier si user connecté, etc.)
        // Pour l'instant, on simule une inscription réussie ou en attente
        
        $this->addFlash('success', 'Votre demande d\'inscription pour "' . $tournoi->getNom() . '" a été prise en compte !');
        
        return $this->redirectToRoute('app_front_tournament_show', ['id' => $tournoi->getId()]);
    }
}
