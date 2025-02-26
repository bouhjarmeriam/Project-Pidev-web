<?php

// src/Controller/PharmacienController.php

namespace App\Controller;

use App\Entity\Pharmacien;
use App\Form\PharmacienType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;

class PharmacienController extends AbstractController
{
    #[Route('/admin/users/create/pharmacien', name: 'pharmacien_create')]
    public function createPharmacien(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // 🔹 Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');

        if (!$userId) {
            $this->addFlash('error', 'Aucun utilisateur trouvé.');
            return $this->redirectToRoute('user_create');
        }

        // 🔹 Trouver l'utilisateur par son ID
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('user_create');
        }

        // Vérifier si l'utilisateur est déjà un pharmacien
        if ($user instanceof Pharmacien) {
            $this->addFlash('error', 'Cet utilisateur est déjà un pharmacien.');
            return $this->redirectToRoute('admin_users');
        }

        // ✅ Convertir User en Pharmacien
        $pharmacien = new Pharmacien();
        $pharmacien->setEmail($user->getEmail());
        $pharmacien->setNom($user->getNom());
        $pharmacien->setPrenom($user->getPrenom());
        $pharmacien->setPassword($user->getPassword());
        $pharmacien->setRoles(array_unique(array_merge($user->getRoles(), ['ROLE_PHARMACIEN'])));

        // 🔹 Création du formulaire de Pharmacien
        $form = $this->createForm(PharmacienType::class, $pharmacien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Remplir les champs spécifiques à Pharmacien
            $pharmacien->setTelephone($form->get('telephone')->getData());

            // 🔹 Supprimer l'ancien User pour éviter un conflit d'ID
            $entityManager->remove($user);
            $entityManager->flush();

            // 🔹 Persister le Pharmacien avec les nouvelles infos
            $entityManager->persist($pharmacien);
            $entityManager->flush();

            // 🔹 Nettoyer la session après conversion
            $session->remove('user_id');

            // ✅ Succès
            $this->addFlash('success', 'Pharmacien créé avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        // 🔹 Afficher le formulaire si non soumis
        return $this->render('user/pharmacien_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
