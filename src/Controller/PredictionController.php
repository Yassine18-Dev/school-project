<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Form\PredictionType;
use App\Repository\PredictionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prediction')]
final class PredictionController extends AbstractController
{
    #[Route(name: 'app_prediction_index', methods: ['GET'])]
    public function index(PredictionRepository $predictionRepository): Response
    {
        return $this->render('prediction/index.html.twig', [
            'predictions' => $predictionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prediction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prediction = new Prediction();
        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prediction);
            $entityManager->flush();

            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prediction/new.html.twig', [
            'prediction' => $prediction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prediction_show', methods: ['GET'])]
    public function show(Prediction $prediction): Response
    {
        return $this->render('prediction/show.html.twig', [
            'prediction' => $prediction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prediction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prediction $prediction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prediction/edit.html.twig', [
            'prediction' => $prediction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prediction_delete', methods: ['POST'])]
    public function delete(Request $request, Prediction $prediction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prediction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prediction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prediction_index', [], Response::HTTP_SEE_OTHER);
    }
}
