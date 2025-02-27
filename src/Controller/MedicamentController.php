<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MedicamentController extends AbstractController
{
    #[Route('/medicament/list', name: 'app_medicament_index', methods: ['GET'])]
    public function index(MedicamentRepository $medicamentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $searchTerm = $request->query->get('search', '');
        $sortField = $request->query->get('sortField', 'm.nom_medicament'); // FIXED FIELD NAME
        $sortOrder = $request->query->get('sortOrder', 'asc');
        $page = $request->query->getInt('page', 1);
    
        // Fetch paginated medications with sorting
        $query = $medicamentRepository->findByFiltersQuery($searchTerm, $sortField, $sortOrder);
    
        $medicaments = $paginator->paginate($query, $page, 10);
    
        return $this->render('medicament/list_medicaments.html.twig', [
            'medicaments' => $medicaments,
            'searchTerm' => $searchTerm,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    #[Route('/medicament/new', name: 'app_medicament_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $medicament = new Medicament();
    $form = $this->createForm(MedicamentType::class, $medicament);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($medicament);
        $entityManager->flush();

        $this->addFlash('success', 'Médicament ajouté avec succès !');
        return $this->redirectToRoute('app_medicament_index');
    }

    return $this->render('medicament/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/medicament/{id}/edit', name: 'app_medicament_edit', methods: ['GET', 'POST'])]
public function edit(int $id, Request $request, MedicamentRepository $medicamentRepository, EntityManagerInterface $entityManager): Response
{
    $medicament = $medicamentRepository->find($id);

    if (!$medicament) {
        throw $this->createNotFoundException('Médicament introuvable.');
    }

    $form = $this->createForm(MedicamentType::class, $medicament);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Médicament modifié avec succès !');
        return $this->redirectToRoute('app_medicament_index');
    }

    return $this->render('medicament/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}

    
    #[Route('/medicament/{id}/delete', name: 'app_medicament_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, Medicament $medicament, EntityManagerInterface $entityManager, MedicamentRepository $medicamentRepository): Response
    {
        $medicament = $medicamentRepository->find($id);

        if (!$medicament) {
            throw $this->createNotFoundException('Le médicament n\'existe pas.');
        }

        if ($this->isCsrfTokenValid('delete' . $medicament->getId(), $request->request->get('_token'))) {
            // Remove relationships before deletion
            foreach ($medicament->getCommandeMedicament() as $commande) {
                $commande->removeMedicament($medicament);
            }

            $entityManager->flush();
            $entityManager->remove($medicament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_medicament_index');
    }
}
