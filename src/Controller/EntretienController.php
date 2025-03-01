<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Entretien;
use App\Entity\Equipement;
use App\Form\EntretienType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntretienRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entretien')]
class EntretienController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/create/{equipement_id}', name: 'create_entretien')]
    public function create(Request $request, int $equipement_id): Response
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
            $this->entityManager->persist($entretien);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'entretien a été créé avec succès !');

            return $this->redirectToRoute('entretien_list');
        }

        return $this->render('entretien/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'entretien_list')]
    public function list(EntretienRepository $entretienRepository): Response
    {
        $entretiens = $entretienRepository->findAll();

        return $this->render('entretien/list.html.twig', [
            'entretiens' => $entretiens,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_entretien')]
    public function edit(int $id, Request $request, EntretienRepository $entretienRepository): Response
    {
        $entretien = $entretienRepository->find($id);

        if (!$entretien) {
            throw $this->createNotFoundException('L\'entretien avec l\'ID '.$id.' n\'existe pas.');
        }

        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'entretien a été modifié avec succès !');

            return $this->redirectToRoute('entretien_list');
        }

        return $this->render('entretien/edit.html.twig', [
            'form' => $form->createView(),
            'entretien' => $entretien,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_entretien')]
    public function delete(int $id): Response
    {
        $entretien = $this->entityManager->getRepository(Entretien::class)->find($id);

        if (!$entretien) {
            throw $this->createNotFoundException('L\'entretien avec l\'ID '.$id.' n\'existe pas.');
        }

        $this->entityManager->remove($entretien);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'entretien a été supprimé avec succès !');

        return $this->redirectToRoute('entretien_list');
    }

    #[Route('/{id}/generer-rapport', name: 'generate_entretien_report')]
    public function generateReport(int $id): Response
    {
        $entretien = $this->entityManager->getRepository(Entretien::class)->find($id);

        if (!$entretien) {
            throw $this->createNotFoundException('Entretien non trouvé');
        }

        $html = $this->renderView('entretien/rapport.html.twig', [
            'entretien' => $entretien,
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="rapport_entretien.pdf"']
        );
    }
}
