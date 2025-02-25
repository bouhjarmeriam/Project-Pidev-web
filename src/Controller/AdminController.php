<?php

 // AdminController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    #[Route('/admin/services', name: 'admin_services')]
    public function services(): Response
    {
        return $this->render('admin/services.html.twig');
    }

    #[Route('/admin/equipment', name: 'admin_equipment')]
    public function equipment(): Response
    {
        return $this->render('admin/equipment.html.twig');
    }

    #[Route('/admin/infrastructure', name: 'admin_infrastructure')]
    public function infrastructure(): Response
    {
        return $this->render('admin/infrastructure.html.twig');
    }

    #[Route('/admin/medical-records', name: 'admin_medical_records')]
    public function medicalRecords(): Response
    {
        return $this->render('admin/medical_records.html.twig');
    }

    #[Route('/admin/medication-stock', name: 'admin_medication_stock')]
    public function medicationStock(): Response
    {
        return $this->render('admin/medication_stock.html.twig');
    }
}

 
