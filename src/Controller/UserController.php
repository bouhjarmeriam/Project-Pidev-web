<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index(UserRepository $userRepository): Response
    {
        // Exemple : récupérer l'utilisateur avec un ID spécifique
        $user = $userRepository->find(1);  // Remplace 1 par l'ID réel que tu veux récupérer

        // Passer l'utilisateur au template
        return $this->render('clinique/index.html.twig', [
            'user' => $user
        ]);
    }
}