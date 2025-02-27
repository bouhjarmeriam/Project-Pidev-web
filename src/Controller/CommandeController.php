<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Commande;
use App\Entity\Medicament;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CommandeController extends AbstractController
{
    #[Route('/command/list', name: 'app_command_list', methods: ['GET'])]
    public function index(Request $request, CommandeRepository $commandeRepository, PaginatorInterface $paginator): Response
    {
        $query = $commandeRepository->createQueryBuilder('c')
            ->orderBy('c.dateCommande', 'DESC') // Order by latest first
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query, // QueryBuilder result
            $request->query->getInt('page', 1), // Get current page
            4 // Items per page (change as needed)
        );
    
        return $this->render('commande/list_commandes.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/command/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
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

                // Calculate total price based on selected medicaments and quantities
                $totalPrix = 0;
                foreach ($commande->getMedicaments() as $medicament) {
                    $totalPrix += $medicament->getPrixMedicament() * $commande->getQuantite();
                }
                $commande->setTotalPrix($totalPrix);

                $entityManager->persist($commande);
                $entityManager->flush();

                $this->addFlash('success', 'Commande enregistrée avec succès !');
                return $this->redirectToRoute('app_command_list');
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

    #[Route('/command/pay/{id}', name: 'app_commande_pay', methods: ['GET'])]
    public function pay(Commande $commande, EntityManagerInterface $em): Response
    {
        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'tnd',
                    'product_data' => [
                        'name' => 'Commande #' . $commande->getId(),
                    ],
                    'unit_amount' => (int) ($commande->getTotalPrix() * 1000),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('app_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $commande->setStripeSessionId($checkout_session->id);
        $em->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/command/payment-success', name: 'app_payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request, EntityManagerInterface $em, CommandeRepository $commandeRepo): Response
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            throw $this->createNotFoundException('Session ID not provided');
        }

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        $commande = $commandeRepo->findOneBy(['stripeSessionId' => $sessionId]);

        if (!$commande) {
            throw $this->createNotFoundException('Commande not found');
        }

        if ($session->payment_status === 'paid') {
            $commande->setStatus('paid');
            $em->flush();
            $this->addFlash('success', 'Paiement réussi ! Merci pour votre achat.');
        } else {
            $this->addFlash('error', 'Le paiement a échoué.');
        }

        return $this->redirectToRoute('app_command_list');
    }

    #[Route('/command/payment-cancel', name: 'app_payment_cancel', methods: ['GET'])]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_command_list');
    }
}