<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\FormNameType; // Correct form type
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/services', name: 'admin_services', methods: ['GET'])]
    public function services(ServiceRepository $serviceRepository): Response
    {
        return $this->render('admin/services_list.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/admin/services/new', name: 'admin_service_new', methods: ['GET', 'POST'])]
    public function newService(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(FormNameType::class, $service); // Use FormNameType

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('admin_services');
        }

        return $this->render('admin/services_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
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
        return $this->render('medicament/new.html.twig');
    }
}
