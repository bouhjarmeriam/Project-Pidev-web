<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_home')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/users', name: 'admin_users')]
    public function users(): Response
    {
        // Placeholder for users management
        return $this->render('admin/users.html.twig');
    }

    #[Route('/services', name: 'admin_services')]
    public function services(): Response
    {
        // Placeholder for services management
        return $this->render('admin/services.html.twig');
    }

    #[Route('/equipment', name: 'admin_equipment')]
    public function equipment(): Response
    {
        // Placeholder for equipment management
        return $this->render('admin/equipment.html.twig');
    }

    #[Route('/dossiers-medicaux', name: 'admin_dossiers_medicaux')]
    public function dossiersMedicaux(): Response
    {
        // Redirect to the dossier medicale admin interface
        return $this->redirectToRoute('admin_dossier_medicale_index');
    }

    #[Route('/medication-stock', name: 'admin_medication_stock')]
    public function medicationStock(): Response
    {
        // Placeholder for medication stock management
        return $this->render('admin/medication_stock.html.twig');
    }
} 