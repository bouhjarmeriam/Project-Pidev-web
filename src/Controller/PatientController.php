<?php

// src/Controller/PatientController.php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\User;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/admin/users/create/patient', name: 'patient_create')]
    public function createPatient(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
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

        // Vérifier si l'utilisateur est déjà un patient
        if ($user instanceof Patient) {
            $this->addFlash('error', 'Cet utilisateur est déjà un patient.');
            return $this->redirectToRoute('admin_users');
        }

        // ✅ Convertir User en Patient (sans supprimer immédiatement l'ancien User)
        $patient = new Patient();
        $patient->setEmail($user->getEmail());
        $patient->setNom($user->getNom());
        $patient->setPrenom($user->getPrenom());
        $patient->setPassword($user->getPassword());
        $patient->setRoles(array_unique(array_merge($user->getRoles(), ['ROLE_PATIENT'])));

        // 🔹 Création du formulaire
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Remplir les champs spécifiques
            $patient->setAdresse($form->get('adresse')->getData());
            $patient->setTelephone($form->get('telephone')->getData());
            $patient->setDateNaissance($form->get('dateNaissance')->getData());

            // 🔹 Supprimer l'ancien User pour éviter un conflit d'ID
            $entityManager->remove($user);
            $entityManager->flush();

            // 🔹 Persister le Patient avec les nouvelles infos
            $entityManager->persist($patient);
            $entityManager->flush();

            // 🔹 Nettoyer la session après conversion
            $session->remove('user_id');

            // ✅ Succès
            $this->addFlash('success', 'Patient créé avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        // 🔹 Afficher le formulaire si non soumis
        return $this->render('user/create_patient.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/patient/edit/{id}', name: 'patient_edit')]
    public function editPatient(Patient $patient, Request $request, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Patient modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/create_patient.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
