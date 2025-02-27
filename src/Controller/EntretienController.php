<?php

// src/Controller/EntretienController.php
namespace App\Controller;

use App\Entity\Entretien;
use App\Entity\Equipement;
use App\Form\EntretienType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntretienRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntretienController extends AbstractController
{
    private $entityManager;

    // Injection de la dépendance Doctrine dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/entretien/create/{equipement_id}', name: 'create_entretien')]
    public function create(Request $request, EntityManagerInterface $entityManager, int $equipement_id): Response
    {
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($equipement_id);

        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé.');
        }

        $entretien = new Entretien();
        $entretien->setEquipement($equipement);
        $entretien->setNomEquipement($equipement->getNom());

        $form = $this->createForm(EntretienType::class, $entretien);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entretien);
            $entityManager->flush();

            $this->addFlash('success', 'L\'entretien a été créé avec succès !');

            return $this->redirectToRoute('entretien_list');
        }

        return $this->render('entretien/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/entretien', name: 'entretien_list')]
    public function list(EntretienRepository $entretienRepository): Response
    {
        $entretiens = $entretienRepository->findAll();

        return $this->render('entretien/list.html.twig', [
            'entretiens' => $entretiens,
        ]);
    }

    #[Route('/entretien/edit/{id}', name: 'edit_entretien')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, EntretienRepository $entretienRepository): Response
    {
        $entretien = $entretienRepository->find($id);

        if (!$entretien) {
            throw $this->createNotFoundException('L\'entretien avec l\'ID '.$id.' n\'existe pas.');
        }

        $form = $this->createForm(EntretienType::class, $entretien);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'entretien a été modifié avec succès !');

            return $this->redirectToRoute('entretien_list');
        }

        return $this->render('entretien/edit.html.twig', [
            'form' => $form->createView(),
            'entretien' => $entretien,
        ]);
    }

    #[Route('/entretien/delete/{id}', name: 'delete_entretien')]
    public function delete(int $id, EntityManagerInterface $entityManager, EntretienRepository $entretienRepository): Response
    {
        $entretien = $entretienRepository->find($id);

        if (!$entretien) {
            throw $this->createNotFoundException('L\'entretien avec l\'ID '.$id.' n\'existe pas.');
        }

        $entityManager->remove($entretien);
        $entityManager->flush();

        $this->addFlash('success', 'L\'entretien a été supprimé avec succès !');

        return $this->redirectToRoute('entretien_list');
    }
}