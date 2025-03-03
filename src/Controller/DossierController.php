<?php

namespace App\Controller;

use App\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dossier')]
class DossierController extends AbstractController
{
    #[Route('/{id}/qr', name: 'app_front_office_dossier_qr_show', methods: ['GET'])]
    public function qrShow(Dossier $dossier): Response
    {
        // Security check - only allow access to the patient's own records or medical staff
        if (!$this->isGranted('ROLE_ADMIN') && 
            $this->getUser() !== $dossier->getPatient() && 
            $this->getUser() !== $dossier->getMedecin()) {
            throw $this->createAccessDeniedException('You cannot access this medical record.');
        }

        return $this->render('front_office/dossier_qr_view.html.twig', [
            'dossier' => $dossier
        ]);
    }

    #[Route('/{id}', name: 'app_front_office_dossier_show', methods: ['GET'])]
    public function show(Dossier $dossier): Response
    {
        // Security check - only allow access to the patient's own records or medical staff
        if (!$this->isGranted('ROLE_ADMIN') && 
            $this->getUser() !== $dossier->getPatient() && 
            $this->getUser() !== $dossier->getMedecin()) {
            throw $this->createAccessDeniedException('You cannot access this medical record.');
        }

        return $this->render('front_office/dossier_show.html.twig', [
            'dossier' => $dossier
        ]);
    }
} 