<?php
namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipementController extends AbstractController
{
    private $entityManager;

    // Injection de la dépendance Doctrine dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route pour afficher la liste des catégories d'équipements
    #[Route('/admin/equipement', name: 'admin_equipment')]
    public function index(): Response
    {
        // Liste des catégories d'équipements
        $categories = [
            'Imagerie médicale',
            'Équipements de laboratoire',
            'Équipements de soins',
            'Équipements d\'urgence',
            'Équipements de chirurgie',
            'Équipements de surveillance',
            'Équipements de stérilisation',
        ];

        // Rendu du template avec les catégories
        return $this->render('admin/equipement.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Route pour afficher une catégorie spécifique
    #[Route('/admin/equipement/{category}', name: 'admin_equipment_category')]
    public function categoryDetails(string $category): Response
    {   
        // Récupérer tous les équipements associés à la catégorie
        $equipements = $this->entityManager->getRepository(Equipement::class)->findBy(['category' => $category]);

        // Rendu du template avec les équipements
        return $this->render('admin/equipement_category.html.twig', [
            'category' => $category,
            'equipements' => $equipements,
        ]);
    }

    // Route pour afficher tous les équipements
    #[Route('/admin/equipements', name: 'admin_all_equipements')]
    public function readEquipements(): Response
    {
        // Récupérer tous les équipements
        $equipements = $this->entityManager->getRepository(Equipement::class)->findAll();

        // Rendu du template avec tous les équipements
        return $this->render('admin/equipements.html.twig', [
            'equipements' => $equipements,
        ]);
    }

    // Route pour afficher le formulaire de création d'un équipement
    #[Route('/admin/equipement/{category}/ajouter', name: 'add_equipement')]
    public function createEquipement(string $category, Request $request): Response
    {
        // Crée une nouvelle instance d'équipement
        $equipement = new Equipement();
        
        // Associer directement la catégorie à l'équipement
        $equipement->setCategory($category);

        // Crée le formulaire pour l'équipement
        $form = $this->createForm(EquipementType::class, $equipement);

        // Gère la requête du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde l'équipement en base de données
            $this->entityManager->persist($equipement);
            $this->entityManager->flush();

            // Redirige après la création vers la catégorie
            return $this->redirectToRoute('admin_equipment_category', ['category' => $category]);
        }

        // Rendu du template avec le formulaire
        return $this->render('admin/ajouter_equipement.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    // Route pour supprimer un équipement
    #[Route('/admin/equipement/{category}/supprimer/{id}', name: 'delete_equipement')]
    public function deleteEquipement(string $category, int $id): Response
    {
        // Lire l'équipement à supprimer
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($id);

        // Si l'équipement existe, on le supprime
        if ($equipement) {
            $this->entityManager->remove($equipement);
            $this->entityManager->flush();
        }

        // Rediriger vers la page de la catégorie après la suppression
        return $this->redirectToRoute('admin_equipment_category', ['category' => $category]);
    }

    // Route pour modifier un équipement
    #[Route('/admin/equipement/{category}/modifier/{id}', name: 'edit_equipement')]
    public function editEquipement(string $category, int $id, Request $request): Response
    {
        // Récupérer l'équipement à modifier
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($id);

        // Si l'équipement n'existe pas, on redirige vers la catégorie
        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé');
        }

        // Crée le formulaire pour l'équipement
        $form = $this->createForm(EquipementType::class, $equipement);

        // Gère la requête du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde l'équipement modifié en base de données
            $this->entityManager->flush();

            // Redirige vers la page de la catégorie après la modification
            return $this->redirectToRoute('admin_equipment_category', ['category' => $category]);
        }

        // Rendu du template avec le formulaire
        return $this->render('admin/ajouter_equipement.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    // Route pour l'entretien d'un équipement
    #[Route('/admin/equipement/{id}/entretien', name: 'entretien_equipement')]
    public function entretienEquipement(int $id): Response
    {
        // Récupérer l'équipement en fonction de son ID
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($id);

        // Vérifier si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé');
        }

        // Rendu du template avec l'équipement pour l'entretien
        return $this->render('admin/entretien_equipement.html.twig', [
            'equipement' => $equipement,
        ]);
    }
}
