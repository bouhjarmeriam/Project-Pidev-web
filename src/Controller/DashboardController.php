<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(): Response
    {
        return $this->render('dashboard/admin.html.twig');
    }

    #[Route('/patient/dashboard', name: 'dashboard_patient')]
    #[IsGranted('ROLE_PATIENT')]
    public function patientDashboard(): Response
    {
        return $this->render('dashboard/patient.html.twig');
    }

    #[Route('/pharmacien/dashboard', name: 'dashboard_pharmacien')]
    #[IsGranted('ROLE_PHARMACIEN')]
    public function pharmacienDashboard(): Response
    {
        return $this->render('dashboard/pharmacien.html.twig');
    }

    #[Route('/medecin/dashboard', name: 'dashboard_medecin')]
    #[IsGranted('ROLE_MEDECIN')]
    public function medecinDashboard(): Response
    {
        return $this->render('dashboard/medecin.html.twig');
    }
    #[Route('/staff/dashboard', name: 'dashboard_staff')]
    #[IsGranted('ROLE_STAFF')]
    public function staffDashboard(): Response
    {
        return $this->render('dashboard/staff.html.twig');
    }
}
