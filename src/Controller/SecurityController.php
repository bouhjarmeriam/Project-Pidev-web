<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirection selon son rôle
        if ($this->getUser()) {
            return $this->redirectToRoute($this->getDashboardRouteByRole());
        }

        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette route est interceptée par le pare-feu de sécurité.');
    }

    private function getDashboardRouteByRole(): string
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return 'dashboard_admin';
        }
        if ($this->isGranted('ROLE_PATIENT')) {
            return 'dashboard_patient';
        }
        if ($this->isGranted('ROLE_PHARMACIEN')) {
            return 'dashboard_pharmacien';
        }
        if ($this->isGranted('ROLE_MEDECIN')) {
            return 'dashboard_medecin';
        }
        if ($this->isGranted('ROLE_STAFF')) {
         return 'dashboard_staff';
        }

        return 'app_home'; 
    }
}
