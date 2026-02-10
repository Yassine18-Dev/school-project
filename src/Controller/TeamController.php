<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(Request $request, TeamRepository $teamRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $sort = (string) $request->query->get('sort', 'id');
        $dir = strtolower((string) $request->query->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // whitelist pour éviter injection via sort
        $allowedSort = ['id', 'name'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $teams = $teamRepository->searchAndSort($q, $sort, $dir);

        return $this->render('team/index.html.twig', [
            'teams' => $teams,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('app_team_index');
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), (string) $request->request->get('_token'))) {
            $em->remove($team);
            $em->flush();
        }

        return $this->redirectToRoute('app_team_index');
    }

    // ---------------- Members management ----------------

    #[Route('/{id}/members', name: 'app_team_members', methods: ['GET'])]
    public function members(Team $team, UserRepository $userRepository): Response
    {
        $available = $userRepository->findAvailablePlayersAndCaptains();

        return $this->render('team/members.html.twig', [
            'team' => $team,
            'availablePlayers' => $available,
        ]);
    }

    #[Route('/{teamId}/add-member/{userId}', name: 'app_team_add_member', methods: ['POST'])]
    public function addMember(int $teamId, int $userId, EntityManagerInterface $em): Response
    {
        $team = $em->getRepository(Team::class)->find($teamId);
        $user = $em->getRepository(User::class)->find($userId);

        if (!$team || !$user) {
            throw $this->createNotFoundException();
        }

        if (!in_array($user->getRoleType(), ['PLAYER', 'CAPTAIN'], true)) {
            $this->addFlash('error', "Cet utilisateur n'est ni PLAYER ni CAPTAIN.");
            return $this->redirectToRoute('app_team_members', ['id' => $teamId]);
        }

        $user->setTeam($team);
        $em->flush();

        $this->addFlash('success', 'Membre ajouté à la team.');
        return $this->redirectToRoute('app_team_members', ['id' => $teamId]);
    }

    #[Route('/{teamId}/remove-member/{userId}', name: 'app_team_remove_member', methods: ['POST'])]
    public function removeMember(int $teamId, int $userId, EntityManagerInterface $em): Response
    {
        $team = $em->getRepository(Team::class)->find($teamId);
        $user = $em->getRepository(User::class)->find($userId);

        if (!$team || !$user) {
            throw $this->createNotFoundException();
        }

        if ($user->getTeam()?->getId() !== $team->getId()) {
            $this->addFlash('error', "Ce membre n'appartient pas à cette team.");
            return $this->redirectToRoute('app_team_members', ['id' => $teamId]);
        }

        $user->setTeam(null);
        $em->flush();

        $this->addFlash('success', 'Membre retiré de la team.');
        return $this->redirectToRoute('app_team_members', ['id' => $teamId]);
    }
}
