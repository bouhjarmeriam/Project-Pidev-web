<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MedicamentCommandeController extends AbstractController{
    #[Route('/medicament/commande', name: 'app_medicament_commande')]
    public function index(): Response
    {
        return $this->render('medicament_commande/index.html.twig', [
            'controller_name' => 'MedicamentCommandeController',
        ]);
    }
}
