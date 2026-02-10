<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUiController extends AbstractController
{
    private function requireAdmin(Request $request): ?Response
    {
        if ($request->query->get('as') !== 'admin') {
            return $this->render('admin/forbidden.html.twig');
        }
        return null;
    }

    #[Route('/admin', name: 'ui_admin', methods: ['GET'])]
    public function dashboard(Request $request): Response
    {
        if ($resp = $this->requireAdmin($request)) return $resp;

        $stats = [
            'users' => 124,
            'reports' => 3,
            'activeToday' => 57,
        ];

        return $this->render('admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/admin/users', name: 'ui_admin_users', methods: ['GET'])]
    public function users(Request $request): Response
    {
        if ($resp = $this->requireAdmin($request)) return $resp;

        $users = [
            ['id'=>1,'username'=>'PlayerOne','email'=>'playerone@arenamind.tn','role'=>'PLAYER','status'=>'ACTIVE'],
            ['id'=>2,'username'=>'CaptainX','email'=>'captainx@arenamind.tn','role'=>'CAPTAIN','status'=>'ACTIVE'],
            ['id'=>3,'username'=>'ToxicGuy','email'=>'toxic@arenamind.tn','role'=>'PLAYER','status'=>'SUSPENDED'],
            ['id'=>4,'username'=>'Spammer','email'=>'spam@arenamind.tn','role'=>'FAN','status'=>'BANNED'],
        ];

        // UI actions (activate/suspend/ban)
        $action = $request->query->get('action');
        $id = $request->query->get('id');
        if ($action && $id) {
            $this->addFlash('success', "Action '$action' applied to user #$id (UI mode).");
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}', name: 'ui_admin_user_show', methods: ['GET'])]
    public function showUser(Request $request, int $id): Response
    {
        if ($resp = $this->requireAdmin($request)) return $resp;

        $u = ['id'=>$id,'username'=>'PlayerOne','email'=>'playerone@arenamind.tn','role'=>'PLAYER','status'=>'ACTIVE'];

        return $this->render('admin/user_show.html.twig', [
            'u' => $u,
        ]);
    }
}
