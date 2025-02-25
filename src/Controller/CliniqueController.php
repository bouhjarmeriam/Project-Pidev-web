<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CliniqueController extends AbstractController{
    #[Route('/clinique', name: 'app_clinique')]
    public function index(): Response
    {
        return $this->render('clinique/index.html.twig', [
            'controller_name' => 'CliniqueController',
        ]);
    }
}
