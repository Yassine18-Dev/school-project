<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserUiController extends AbstractController
{
    private function getFakeUser(Request $request): array
    {
        $role = $request->query->get('as') === 'admin' ? 'ADMIN' : 'PLAYER';

        return [
            'username' => 'PlayerOne',
            'email' => 'playerone@arenamind.tn',
            'role' => $role,
            'status' => 'ACTIVE',
            'registeredAt' => '2026-02-01',
            'lastActivity' => 'Today 14:30',
        ];
    }

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

    #[Route('/profile', name: 'ui_profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $this->getFakeUser($request),
        ]);
    }

    #[Route('/profile/edit', name: 'ui_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request): Response
    {
        $user = [
            'username' => 'PlayerOne',
            'email' => 'playerone@arenamind.tn',
            'bio' => 'Esports fan • Valorant main • Campus competitor',
            'favoriteGame' => 'Valorant',
        ];

        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Profile updated (UI mode).');
            return $this->redirectToRoute('ui_profile');
        }

        return $this->render('user/edit_profile.html.twig', [
            'user' => $user,
        ]);
    }

    // Request reset link
    #[Route('/forgot-password', name: 'ui_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $emailTo = trim((string) $request->request->get('email'));

            $token = bin2hex(random_bytes(16));
            $request->getSession()->set('reset_token', $token);

            $resetPath = $this->generateUrl('ui_reset_password', ['token' => $token], 0);
            $resetLink = $request->getSchemeAndHttpHost() . $resetPath;

            $email = (new Email())
                ->from('no-reply@arenamind.tn')
                ->to($emailTo)
                ->subject('ArenaMind - Reset your password')
                ->html($this->renderView('emails/reset_password.html.twig', [
                    'resetLink' => $resetLink,
                    'email' => $emailTo,
                ]));

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Reset link sent! Check your email.');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Mailer not configured. Use this link: ' . $resetLink);
            }

            return $this->redirectToRoute('ui_forgot_password');
        }

        return $this->render('user/forgot_password.html.twig');
    }

    // Set new password (UI-only)
    #[Route('/reset-password/{token}', name: 'ui_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, string $token): Response
    {
        $sessionToken = $request->getSession()->get('reset_token');

        if (!$sessionToken || $token !== $sessionToken) {
            return $this->render('user/reset_password.html.twig', [
                'valid' => false,
            ]);
        }

        if ($request->isMethod('POST')) {
            $request->getSession()->remove('reset_token');
            $this->addFlash('success', 'Password updated (UI mode). Now you can login.');
            return $this->redirectToRoute('ui_login');
        }

        return $this->render('user/reset_password.html.twig', [
            'valid' => true,
        ]);
    }
}
