<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class UserUiController extends AbstractController
{
    private function getFakeUser(Request $request): User
    {
        // On crée un User fictif pour l'affichage si aucun user connecté
        $user = new User();
        $user->setUsername('PlayerOne');
        $user->setEmail('playerone@arenamind.tn');
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setBio('Compte de démonstration (UI Check)');
        $user->setFavoriteGame('Valorant');
        
        $role = $request->query->get('as') === 'admin' ? 'ADMIN' : 'PLAYER';
        if ($role === 'ADMIN') {
            $user->setRoles(['ROLE_ADMIN']);
            $user->setRoleType('ADMIN'); // Si vous avez ce champ
        } else {
            $user->setRoleType('PLAYER');
        }

        // Pour les dates, on triche un peu car pas de setters publics pour createdAt parfois
        // Mais votre entité a un constructeur pour createdAt.
        // lastActivityAt a un setter.
        $user->setLastActivityAt(new \DateTimeImmutable());

        return $user;
    }

    // NOTE: Si ces routes existent déjà dans SecurityController/RegistrationController,
    // Symfony prendra la première chargée. Pour éviter les conflits,
    // on peut commenter celles qui sont gérées ailleurs ou s'assurer qu'elles
    // pointent vers les mêmes templates.

    /*
    #[Route('/login', name: 'ui_login', methods: ['GET'])]
    public function login(): Response
    {
        return $this->render('user/login.html.twig');
    }

    #[Route('/register', name: 'ui_register', methods: ['GET'])]
    public function register(): Response
    {
        return $this->render('user/register.html.twig');
    }
    */

    #[Route('/profile', name: 'ui_profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            // Fallback sur le fake user si pas connecté (pour test UI)
            $user = $this->getFakeUser($request);
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'ui_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            // Mode démo sans sauvegarde réelle
            $user = $this->getFakeUser($request);
            if ($request->isMethod('POST')) {
                $this->addFlash('success', 'Profil mis à jour (Mode Simulation UI).');
                return $this->redirectToRoute('ui_profile');
            }
        } else {
            // Mode réel avec sauvegarde DB
            if ($request->isMethod('POST')) {
                $user->setUsername($request->request->get('username'));
                $user->setEmail($request->request->get('email'));
                $user->setBio($request->request->get('bio'));
                $user->setFavoriteGame($request->request->get('favoriteGame'));
                
                $em->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès !');
                return $this->redirectToRoute('ui_profile');
            }
        }

        return $this->render('user/edit_profile.html.twig', [
            'user' => $user,
        ]);
    }

    // Request reset link
    /*
    #[Route('/forgot-password', name: 'ui_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, MailerInterface $mailer): Response
    {
        // ... (Logic commented out to use real ResetPasswordController) ...
        return $this->render('user/forgot_password.html.twig');
    }

    // Set new password (UI-only)
    #[Route('/reset-password/{token}', name: 'ui_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, string $token): Response
    {
        // ... (Logic commented out to use real ResetPasswordController) ...
        return $this->render('user/reset_password.html.twig', [
            'valid' => true,
        ]);
    }
    */
}

