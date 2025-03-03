<?php

namespace App\Controller;

use App\Entity\Etage;
use App\Form\EtageType;
use App\Repository\EtageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/etage')]
class EtageController extends AbstractController
{
    #[Route('/', name: 'etage_index', methods: ['GET'])]
// src/Controller/EtageController.php

#[Route('/', name: 'etage_index', methods: ['GET'])]
public function index(
    EtageRepository $etageRepository,
    Request $request,
    PaginatorInterface $paginator
): Response {
    // Get search and sort parameters from the request
    $search = $request->query->get('search', '');
    $sortBy = $request->query->get('sort_by', 'Numero'); // Default sort by 'Numero'
    $sortOrder = $request->query->get('sort_order', 'asc'); // Default order 'asc'

    // Fetch filtered and sorted étages
    $query = $etageRepository->findBySearchAndSort($search, $sortBy, $sortOrder);

    // Paginate the results
    $etages = $paginator->paginate(
        $query, // Query to paginate
        $request->query->getInt('page', 1), // Page number, default to 1
        2 // Items per page
    );

    // Calculate max_page for pagination
    $maxPage = ceil($etages->getTotalItemCount() / $etages->getItemNumberPerPage());

    return $this->render('etage/index.html.twig', [
        'etages' => $etages,
        'search' => $search,
        'sort_by' => $sortBy,
        'sort_order' => $sortOrder,
        'current_page' => $etages->getCurrentPageNumber(), // Pass current page
        'max_page' => $maxPage, // Pass max page
    ]);
}

    #[Route('/new', name: 'etage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etage = new Etage();
        $form = $this->createForm(EtageType::class, $etage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etage);
            $entityManager->flush();

            return $this->redirectToRoute('etage_index');
        }

        return $this->render('etage/add.html.twig', [
            'etage' => $etage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'etage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etage $etage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtageType::class, $etage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('etage_index');
        }

        return $this->render('etage/edit.html.twig', [
            'etage' => $etage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'etage_delete', methods: ['POST'])]
    public function delete(Request $request, Etage $etage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('etage_index');
    }
    
    #[Route('/etage/{id}/salles', name: 'etage_salles_show', methods: ['GET'])]
    public function showSalles(Etage $etage): Response
    {
        // Récupérer les salles liées à cet étage
        $salles = $etage->getSalle();
    
        return $this->render('etage/show_salles.html.twig', [
            'etage' => $etage,
            'salles' => $salles,
        ]);
    }
}