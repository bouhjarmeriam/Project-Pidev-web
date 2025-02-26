<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Form\StaffType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;

class StaffController extends AbstractController
{
    #[Route('/admin/users/create/staff', name: 'staff_create')]
    public function createStaff(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            $this->addFlash('error', 'Aucun utilisateur trouvé.');
            return $this->redirectToRoute('user_create');
        }

        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('user_create');
        }

        if ($user instanceof Staff) {
            $this->addFlash('error', 'Cet utilisateur est déjà un membre du staff.');
            return $this->redirectToRoute('admin_users');
        }

        $staff = new Staff();
        $staff->setEmail($user->getEmail());
        $staff->setNom($user->getNom());
        $staff->setPrenom($user->getPrenom());
        $staff->setPassword($user->getPassword());
        $staff->setRoles(array_unique(array_merge($user->getRoles(), ['ROLE_STAFF'])));

        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $staff->setTelephone($form->get('telephone')->getData());

            $entityManager->remove($user);
            $entityManager->flush();

            $entityManager->persist($staff);
            $entityManager->flush();

            $session->remove('user_id');

            $this->addFlash('success', 'Membre du staff créé avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/staff_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
