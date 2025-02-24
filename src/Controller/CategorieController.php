<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController{
    #[Route('/categories', name: 'app_categories', methods: ['GET'])]
    public function index(): Response
    {
        // Définition des catégories en dur
        $categories = [
            ['nom' => 'Général', 'icone' => 'general.png', 'slug' => 'general'],
            ['nom' => 'Peau & Cheveux', 'icone' => 'skin_hair.png', 'slug' => 'skin-hair'],
            ['nom' => 'Soins pour enfants', 'icone' => 'child_care.png', 'slug' => 'child-care'],
            ['nom' => 'Santé des femmes', 'icone' => 'woman_health.png', 'slug' => 'woman-health'],
            ['nom' => 'Dentiste', 'icone' => 'dentist.png', 'slug' => 'dentist'],
            ['nom' => 'ORL', 'icone' => 'ent.png', 'slug' => 'ent'],
            ['nom' => 'Vitamines', 'icone' => 'vitamins.png', 'slug' => 'vitamins']
        ];

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
