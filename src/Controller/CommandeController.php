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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use MyPdfBundle\Services\PdfGenerator;
use Psr\Log\LoggerInterface;


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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du stock
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
                // Mise à jour du stock
                foreach ($commande->getMedicaments() as $medicament) {
                    $newStock = $medicament->getQuantiteStock() - $commande->getQuantite();
                    $medicament->setQuantiteStock($newStock);
                    $entityManager->persist($medicament);
                }

                // Calcul du prix total
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
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recalcul du prix total
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
                    'unit_amount' => (int) ($commande->getTotalPrix()),
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
    public function paymentSuccess(
        Request $request,
        EntityManagerInterface $em,
        CommandeRepository $commandeRepo,
        PdfGenerator $pdfGenerator,
        LoggerInterface $logger
    ): Response {
        $logger->info('Début du traitement paymentSuccess');
        
        try {
            $sessionId = $request->query->get('session_id');
            $logger->debug('Session ID reçu: '.$sessionId);

            if (!$sessionId) {
                $this->addFlash('error', 'Session ID manquant.');
                return $this->redirectToRoute('app_command_list');
            }

            \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $logger->debug('Statut paiement Stripe: '.$session->payment_status);

            $commande = $commandeRepo->findOneBy(['stripeSessionId' => $sessionId]);
            if (!$commande) {
                $logger->error('Commande non trouvée pour session: '.$sessionId);
                throw $this->createNotFoundException('Aucune commande correspondante trouvée.');
            }

            if ($session->payment_status === 'paid') {
                $logger->info('Paiement confirmé, mise à jour de la commande ID: '.$commande->getId());
                
                $commande
                    ->setStatus('payé')
                    ->setDateCommande(new \DateTime());

                $em->flush();

                // Génération PDF
                $logger->info('Génération du PDF pour commande ID: '.$commande->getId());
                $pdfContent = $pdfGenerator->generateCommandePdf($commande);

                return new Response(
                    $pdfContent,
                    Response::HTTP_OK,
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="facture_'.$commande->getId().'.pdf"'
                    ]
                );
            }

            $this->addFlash('warning', 'Paiement non confirmé.');
        } catch (\Exception $e) {
            $logger->critical('Erreur paymentSuccess: '.$e->getMessage().' - Trace: '.$e->getTraceAsString());
            $this->addFlash('error', 'Erreur lors du traitement : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_command_list');
    }

    #[Route('/command/payment-cancel', name: 'app_payment_cancel', methods: ['GET'])]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_command_list');
    }



    #[Route('/command/download/{id}', name: 'app_commande_download', methods: ['GET'])]
    public function downloadPdf(Commande $commande, PdfGenerator $pdfGenerator): Response
    {
        $pdfContent = $pdfGenerator->generateCommandePdf($commande);
        
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="facture_'.$commande->getId().'.pdf"'
        ]);
    }
}