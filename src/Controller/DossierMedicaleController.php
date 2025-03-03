<?php

namespace App\Controller;

use App\Entity\DossierMedicale;
use App\Form\DossierMedicaleType;
use App\Repository\DossierMedicaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/dossier-medicale')]
class DossierMedicaleController extends AbstractController
{
    #[Route('/', name: 'app_dossier_medicale_index', methods: ['GET'])]
    public function index(DossierMedicaleRepository $dossierMedicaleRepository): Response
    {
        return $this->render('dossier_medicale/index.html.twig', [
            'dossier_medicales' => $dossierMedicaleRepository->findAll(),
        ]);
    }

    #[Route('/admin', name: 'admin_dossier_medicale_index', methods: ['GET'])]
    public function adminIndex(DossierMedicaleRepository $dossierMedicaleRepository): Response
    {
        return $this->render('dossier_medicale/index.html.twig', [
            'dossier_medicales' => $dossierMedicaleRepository->findAll(),
            'is_admin' => true,
        ]);
    }

    #[Route('/new', name: 'app_dossier_medicale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $dossierMedicale = new DossierMedicale();
        $form = $this->createForm(DossierMedicaleType::class, $dossierMedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('dossiers_directory'),
                        $newFilename
                    );
                    $dossierMedicale->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->persist($dossierMedicale);
            $entityManager->flush();

            $this->addFlash('success', 'Le dossier médical a été créé avec succès.');
            return $this->redirectToRoute('app_dossier_medicale_index');
        }

        return $this->render('dossier_medicale/new.html.twig', [
            'dossier_medicale' => $dossierMedicale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/calendar', name: 'app_dossier_medicale_calendar', methods: ['GET'])]
    public function calendar(DossierMedicale $dossierMedicale): Response
    {
        return $this->render('dossier_medicale/calendar.html.twig', [
            'dossier' => $dossierMedicale,
        ]);
    }

    #[Route('/{id}', name: 'app_dossier_medicale_show', methods: ['GET'])]
    public function show(DossierMedicale $dossierMedicale): Response
    {
        return $this->render('dossier_medicale/show.html.twig', [
            'dossier_medicale' => $dossierMedicale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dossier_medicale_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DossierMedicale $dossierMedicale, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(DossierMedicaleType::class, $dossierMedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('dossiers_directory'),
                        $newFilename
                    );
                    $dossierMedicale->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le dossier médical a été modifié avec succès.');
            return $this->redirectToRoute('app_dossier_medicale_index');
        }

        return $this->render('dossier_medicale/edit.html.twig', [
            'dossier_medicale' => $dossierMedicale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dossier_medicale_delete', methods: ['POST'])]
    public function delete(Request $request, DossierMedicale $dossierMedicale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dossierMedicale->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dossierMedicale);
            $entityManager->flush();
            $this->addFlash('success', 'Le dossier médical a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_dossier_medicale_index');
    }
}