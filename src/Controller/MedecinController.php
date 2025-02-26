<?php
namespace App\Controller;

use App\Entity\Medecin;
use App\Form\MedecinType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;

class MedecinController extends AbstractController
{
    #[Route('/admin/users/create/medecin', name: 'medecin_create')]
    public function createMedecin(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
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

        // Vérifier si l'utilisateur est déjà un médecin
        if ($user instanceof Medecin) {
            $this->addFlash('error', 'Cet utilisateur est déjà un médecin.');
            return $this->redirectToRoute('admin_users');
        }

        // ✅ Convertir User en Medecin (sans supprimer l'ancien User pour l'instant)
        $medecin = new Medecin();
        $medecin->setEmail($user->getEmail());
        $medecin->setNom($user->getNom());
        $medecin->setPrenom($user->getPrenom());
        $medecin->setPassword($user->getPassword());
        $medecin->setRoles(array_unique(array_merge($user->getRoles(), ['ROLE_MEDECIN'])));

        // 🔹 Création du formulaire
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Remplir les champs spécifiques
            $medecin->setSpecialite($form->get('specialite')->getData());
            $medecin->setTelephone($form->get('telephone')->getData());

            // 🔹 Supprimer l'ancien User pour éviter un conflit d'ID
            $entityManager->remove($user);
            $entityManager->flush();

            // 🔹 Persister le Medecin avec les nouvelles infos
            $entityManager->persist($medecin);
            $entityManager->flush();

            // 🔹 Nettoyer la session après conversion
            $session->remove('user_id');

            // ✅ Succès
            $this->addFlash('success', 'Médecin créé avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        // 🔹 Afficher le formulaire si non soumis
        return $this->render('user/medecin_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
