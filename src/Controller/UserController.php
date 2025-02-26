<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class UserController extends AbstractController
{
    #[Route('/admin/users/create', name: 'user_create')]
    public function create(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SessionInterface $session,
        
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Attribuer les rôles en fonction du type d'utilisateur
            $type = $form->get('type')->getData();
            $roles = match ($type) {
                'patient' => ['ROLE_PATIENT'],
                'medecin' => ['ROLE_MEDECIN'],
                'pharmacien' => ['ROLE_PHARMACIEN'],
                'staff' => ['ROLE_STAFF'],
                default => ['ROLE_USER'],
            };
            $user->setRoles($roles);

            // Sauvegarde en base
            $entityManager->persist($user);
            $entityManager->flush();

            // Si le type est patient, rediriger vers la création du patient
            if ($type === 'patient') {
                $session->set('user_id', $user->getId());
                return $this->redirectToRoute('patient_create');
            }
            if ($type === 'medecin') {
                $session->set('user_id', $user->getId());
                return $this->redirectToRoute('medecin_create');
            }
            if ($type === 'pharmacien') {
                $session->set('user_id', $user->getId());
                return $this->redirectToRoute('pharmacien_create');
            }
            if ($type === 'staff') {
                $session->set('user_id', $user->getId());
                return $this->redirectToRoute('staff_create');
            }


            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function read(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('/admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'user_delete')]
    public function delete(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/{id}/edit', name: 'user_edit')]
    public function edit(
        User $user, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    


 
}