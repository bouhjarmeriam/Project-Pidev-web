<?php
namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/salle')]
class SalleController extends AbstractController
{
    #[Route('/', name: 'salle_index', methods: ['GET'])]
    public function index(
        SalleRepository $salleRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer le paramètre de recherche (s'il existe)
        $search = $request->query->get('search', '');
    
        // Récupérer le paramètre de tri (s'il existe)
        $sortBy = $request->query->get('sort_by', 'nom'); // Par défaut, trier par 'nom'
        $sortOrder = $request->query->get('sort_order', 'asc'); // Par défaut, ordre ascendant
    
        // Créer un QueryBuilder pour les salles avec recherche et tri
        $queryBuilder = $salleRepository->createQueryBuilder('s');
    
        // Appliquer la recherche si un terme est fourni
        if ($search) {
            $queryBuilder->andWhere('s.nom LIKE :search OR s.type_salle LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        // Appliquer le tri
        $queryBuilder->orderBy('s.' . $sortBy, $sortOrder);
    
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page par défaut
            2 // Nombre d'éléments par page
        );
    
        return $this->render('salle/index.html.twig', [
            'salles' => $pagination,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'current_page' => $pagination->getCurrentPageNumber(),
            'max_page' => $pagination->getPageCount(),
        ]);
    }
    #[Route('/listmedecin', name:'salle_listmedecin', methods: ['GET'])]
    
   public function listMedecin(SalleRepository $salleRepository, Request $request, PaginatorInterface $paginator): Response
   {  $queryBuilder = $salleRepository->createQueryBuilder('salle');
    $pagination = $paginator->paginate(
        $queryBuilder, 
        $request->query->getInt('page', 1), // Page courante
        2 // Nombre d'éléments par page
    );
       
       return $this->render('salle/listmedecin.html.twig', [
        'salles' => $pagination,
        'current_page' => $pagination->getCurrentPageNumber(),
        'max_page' => $pagination->getPageCount(),  
       ]);
   }

    #[Route('/add', name: 'salle_add', methods: ['GET', 'POST'])]
    public function newDepartement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('upload_directory'), $imageName);
                $salle->setImage('uploads/'.$imageName);
            }

            $entityManager->persist($salle);
            $entityManager->flush();

            $this->addFlash('success', 'Salle ajoutée avec succès !');
            return $this->redirectToRoute('salle_index');
        }

        return $this->render('salle/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        $oldImage = $salle->getImage();

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $uploadPath = $this->getParameter('upload_directory');
                $image->move($uploadPath, $imageName);
                $salle->setImage('uploads/' . $imageName);

                if ($oldImage && file_exists($oldImage)) {
                    unlink($oldImage);
                }
            } else {
                $salle->setImage($oldImage);
            }

            $entityManager->persist($salle);
            $entityManager->flush();

            $this->addFlash('success', 'Salle mise à jour avec succès !');
            return $this->redirectToRoute('salle_index');
        }

        return $this->render('salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'salle_delete', methods: ['POST'])]
    public function delete(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($salle);
            $entityManager->flush();
            $this->addFlash('success', 'Salle supprimée avec succès !');
        }

        return $this->redirectToRoute('salle_index');
    }

    // 1️⃣ Affichage des détails d’une salle
    #[Route('/{id}/show', name: 'salle_show', methods: ['GET'])]
    public function show(Salle $salle): Response
    {
        return $this->render('salle/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    // 2️⃣ Filtrage des salles par statut (Disponible, Occupé, Maintenance)
    #[Route('/filter/{status}', name: 'salle_filter', methods: ['GET'])]
    public function filter(SalleRepository $salleRepository, string $status): Response
    {
        $salles = $salleRepository->findBy(['status' => ucfirst($status)]);

        return $this->render('salle/index.html.twig', [
            'salles' => $salles,
        ]);
    }

    // 3️⃣ Exportation des salles en CSV
    #[Route('/export/csv', name: 'salle_export', methods: ['GET'])]
    public function export(SalleRepository $salleRepository): Response
    {
        $response = new StreamedResponse(function () use ($salleRepository) {
            $handle = fopen('php://output', 'w');

            // En-tête du fichier CSV
            fputcsv($handle, ['ID', 'Nom', 'Capacité', 'Type', 'Étage', 'Status']);

            // Données des salles
            foreach ($salleRepository->findAll() as $salle) {
                fputcsv($handle, [
                    $salle->getId(),
                    $salle->getNom(),
                    $salle->getCapacite(),
                    $salle->getTypeSalle(),
                    $salle->getEtage() ? $salle->getEtage()->getId() : 'Non défini',
                    $salle->getStatus()
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="salles.csv"');

        return $response;
    }
}
