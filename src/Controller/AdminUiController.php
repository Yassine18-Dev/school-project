<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUiController extends AbstractController
{
    #[Route('/admin', name: 'ui_admin', methods: ['GET'])]
    public function dashboard(UserRepository $repo): Response
{
    $total = $repo->count([]);

    $active = $repo->count(['status' => User::STATUS_ACTIVE]);
    $suspended = $repo->count(['status' => User::STATUS_SUSPENDED]);
    $banned = $repo->count(['status' => User::STATUS_BANNED]);

    // ✅ Online = lastActivityAt >= now - 10 minutes
    $online = $repo->countOnlineUsers(10);

    // ✅ Mini chart 7 jours (combien de users ont été “actifs” par jour)
    $days = [];
    $activity = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = (new \DateTimeImmutable('today'))->modify("-{$i} days");
        $days[] = $date->format('D'); // Mon Tue Wed...
        $activity[] = $repo->countActiveOnDate($date);
    }

    return $this->render('admin/dashboard.html.twig', [
        'stats' => [
            'users' => $total,
            'active' => $active,
            'suspended' => $suspended,
            'banned' => $banned,
            'online' => $online,
        ],
        'chart' => [
            'days' => $days,
            'activity' => $activity,
        ],
    ]);
}

    #[Route('/admin/users', name: 'ui_admin_users', methods: ['GET'])]
    public function users(Request $request, UserRepository $repo, EntityManagerInterface $em): Response
    {
        // --- Lire paramètres (Recherche + Tri) ---
        $q    = trim((string) $request->query->get('q', ''));
        $sort = (string) $request->query->get('sort', 'id');
        $dir  = (string) $request->query->get('dir', 'DESC');
        $dir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        // --- Actions status (Activate/Suspend/Ban) ---
        $action = (string) $request->query->get('action', '');
        $id = $request->query->getInt('id', 0);

        if ($action && $id) {
            $u = $repo->find($id);

            if ($u instanceof User) {
                if ($action === 'activate') $u->setStatus(User::STATUS_ACTIVE);
                if ($action === 'suspend')  $u->setStatus(User::STATUS_SUSPENDED);
                if ($action === 'ban')      $u->setStatus(User::STATUS_BANNED);

                $em->flush();
                $this->addFlash('success', "User #$id updated: $action");
            }

            // IMPORTANT: rediriger pour éviter resoumission + garder q/sort/dir
            return $this->redirectToRoute('ui_admin_users', [
                'q' => $q,
                'sort' => $sort,
                'dir' => $dir,
            ]);
        }

        // --- Requête DB: recherche + tri ---
        $users = $repo->qbSearchSort($q, $sort, $dir)
            ->getQuery()
            ->getResult();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    #[Route('/admin/users/{id}', name: 'ui_admin_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $repo): Response
    {
        $u = $repo->find($id);
        if (!$u) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('admin/user_show.html.twig', ['u' => $u]);
    }
}
