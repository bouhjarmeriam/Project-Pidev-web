<?php
// src/Controller/RapportController.php

namespace App\Controller;

use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapports')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'rapport_list')]
    public function list(RapportRepository $rapportRepository): Response
    {
        // Récupérer tous les rapports depuis la base de données
        $rapports = $rapportRepository->findAll();

        // Afficher la liste des rapports
        return $this->render('rapport/list.html.twig', [
            'rapports' => $rapports,
        ]);
    }
}
