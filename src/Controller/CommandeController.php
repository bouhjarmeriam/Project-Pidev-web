<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Medicament;
use App\Form\CommandeType;
use App\Form\MedicamentType;
use App\Repository\CommandeRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CommandeController extends AbstractController{
#[Route('/command/list',name: 'app_command_list', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/list_commandes.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }


 #[Route('/command/new',name: 'app_commande_new', methods: ['GET' ,'POST'])]
 public function new(Request $request, EntityManagerInterface $entityManager, MedicamentRepository $medicamentRepository): Response
{
    $commande = new Commande();
    $commande->setDateCommande(new \DateTime());

    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $nouveauMedicamentNom = $form->get('nouveauMedicament')->getData();

    if ($nouveauMedicamentNom) {
         $medicamentExist = $medicamentRepository->findOneBy(['nom_medicament' => $nouveauMedicamentNom]);

         if (!$medicamentExist) {
             $nouveauMedicament = new Medicament();
             $nouveauMedicament->setNomMedicament($nouveauMedicamentNom);
             $nouveauMedicament->setQuantiteStock(0); // Initialiser le stock à 0

             $entityManager->persist($nouveauMedicament);
             $commande->addMedicament($nouveauMedicament);
         } else {
             $commande->addMedicament($medicamentExist);
         }
     }
     if ($form->isSubmitted() && $form->isValid()) {
        // Calculate total price based on selected medicaments and quantities
        $totalPrix = 0;
        foreach ($commande->getMedicaments() as $medicament) {
            $totalPrix += $medicament->getPrixMedicament() * $commande->getQuantite();
        }
        $commande->setTotalPrix($totalPrix); // 🔴 FIX: Set total price before saving
    
        $entityManager->persist($commande);
        $entityManager->flush();
    
        $this->addFlash('success', 'Commande ajoutée avec succès !');
        return $this->redirectToRoute('app_command_list');
    }

     // Vérifier la disponibilité du stock
     $stockInsufficient = false;
     foreach ($commande->getMedicaments() as $medicament) {
         if ($commande->getQuantite() > $medicament->getQuantiteStock()) {
             $stockInsufficient = true;
             break;
         }
     }

     if ($stockInsufficient) {
         $this->addFlash('error', 'Stock insuffisant pour cette commande.');
     } else {
         // Réduire le stock des médicaments existants
         foreach ($commande->getMedicaments() as $medicament) {
             $newStock = $medicament->getQuantiteStock() - $commande->getQuantite();
             $medicament->setQuantiteStock($newStock);
             $entityManager->persist($medicament);
         }

         $entityManager->persist($commande);
         $entityManager->flush();

         $this->addFlash('success', 'Commande enregistrée avec succès !');
         return $this->redirectToRoute('app_commande_index');
     }
    }

        return $this->render('commande/new.html.twig', [
        'form' => $form->createView(),
        ]);
    }


    
    #[Route('/command/edit/{id}', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, MedicamentRepository $medicamentRepository): Response
    {
    $commande = $commandeRepository->find($id);

    if (!$commande) {
        throw $this->createNotFoundException('Commande non trouvée.');
    }

    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si un nouveau médicament a été ajouté
        $nouveauMedicamentNom = $form->get('nouveauMedicament')->getData();

        if ($nouveauMedicamentNom) {
            $medicamentExist = $medicamentRepository->findOneBy(['nom_medicament' => $nouveauMedicamentNom]);

            if (!$medicamentExist) {
                $nouveauMedicament = new Medicament();
                $nouveauMedicament->setNomMedicament($nouveauMedicamentNom);
                $nouveauMedicament->setQuantiteStock(0); // Initialiser à 0

                $entityManager->persist($nouveauMedicament);
                $commande->addMedicament($nouveauMedicament);
            } else {
                $commande->addMedicament($medicamentExist);
            }
        }

        // Recalculer le prix total
        $totalPrix = 0;
        foreach ($commande->getMedicaments() as $medicament) {
            $totalPrix += $medicament->getPrixMedicament() * $commande->getQuantite();
        }
        $commande->setTotalPrix($totalPrix);

        $entityManager->flush();

        $this->addFlash('success', 'Commande modifiée avec succès !');
        return $this->redirectToRoute('app_command_list');
    }

    return $this->render('commande/edit.html.twig', [
        'form' => $form->createView(),
        'commande' => $commande,
    ]);
    }


    #[Route('/command/delete/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Commande supprimée avec succès !');
        } else {
            $this->addFlash('error', 'Échec de la suppression de la commande.');
        }

        return $this->redirectToRoute('app_command_list', [], Response::HTTP_SEE_OTHER);
    }
}
