<?php

namespace App\Controller;

use App\Repository\TournoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontTournoiController extends AbstractController
{
    #[Route('/evenements', name: 'app_front_tournois')]
    public function index(TournoiRepository $tournoiRepository): Response
    {
        // On récupère les tournois pour les afficher sur le front
        return $this->render('front/tournois_list.html.twig', [
            'tournois' => $tournoiRepository->findAll(),
        ]);
    }
}