<?php

namespace App\Controller;

use App\Entity\Sejour;
use App\Form\SejourType;
use App\Repository\SejourRepository;
use App\Repository\DossierMedicaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sejour')]
class SejourController extends AbstractController
{
    #[Route('/', name: 'app_sejour_index', methods: ['GET'])]
    public function index(SejourRepository $sejourRepository): Response
    {
        $sejours = $sejourRepository->findAll();
        
        // Calculate statistics
        $stats = [
            'total' => count($sejours),
            'totalRevenue' => 0,
            'averageStayDuration' => 0,
            'typeDistribution' => [
                'Hospitalisation' => 0,
                'Consultation' => 0,
                'Urgence' => 0
            ],
            'paymentStatus' => [
                'Payé' => 0,
                'En attente' => 0,
                'Annulé' => 0
            ],
            'currentMonth' => [
                'count' => 0,
                'revenue' => 0
            ],
            'previousMonth' => [
                'count' => 0,
                'revenue' => 0
            ]
        ];
        
        $totalDuration = 0;
        $now = new \DateTime();
        $currentMonthStart = new \DateTime('first day of this month');
        $previousMonthStart = new \DateTime('first day of last month');
        $previousMonthEnd = new \DateTime('last day of last month');
        
        foreach ($sejours as $sejour) {
            // Total revenue
            $stats['totalRevenue'] += $sejour->getFraisSejour();
            
            // Stay duration
            $interval = $sejour->getDateEntree()->diff($sejour->getDateSortie());
            $durationInDays = $interval->days;
            $totalDuration += $durationInDays;
            
            // Type distribution
            if (isset($stats['typeDistribution'][$sejour->getTypeSejour()])) {
                $stats['typeDistribution'][$sejour->getTypeSejour()]++;
            }
            
            // Payment status
            if (isset($stats['paymentStatus'][$sejour->getStatutPaiement()])) {
                $stats['paymentStatus'][$sejour->getStatutPaiement()]++;
            }
            
            // Current month stats
            if ($sejour->getDateEntree() >= $currentMonthStart) {
                $stats['currentMonth']['count']++;
                $stats['currentMonth']['revenue'] += $sejour->getFraisSejour();
            }
            
            // Previous month stats
            if ($sejour->getDateEntree() >= $previousMonthStart && $sejour->getDateEntree() <= $previousMonthEnd) {
                $stats['previousMonth']['count']++;
                $stats['previousMonth']['revenue'] += $sejour->getFraisSejour();
            }
        }
        
        // Calculate average stay duration
        if ($stats['total'] > 0) {
            $stats['averageStayDuration'] = round($totalDuration / $stats['total'], 1);
        }
        
        // Calculate month-over-month growth
        $stats['monthGrowth'] = [
            'count' => $stats['previousMonth']['count'] > 0 
                ? round(($stats['currentMonth']['count'] - $stats['previousMonth']['count']) / $stats['previousMonth']['count'] * 100, 1) 
                : 0,
            'revenue' => $stats['previousMonth']['revenue'] > 0 
                ? round(($stats['currentMonth']['revenue'] - $stats['previousMonth']['revenue']) / $stats['previousMonth']['revenue'] * 100, 1) 
                : 0
        ];
        
        return $this->render('sejour/index.html.twig', [
            'sejours' => $sejours,
            'stats' => $stats
        ]);
    }

    #[Route('/new', name: 'app_sejour_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        DossierMedicaleRepository $dossierMedicaleRepository
    ): Response
    {
        $sejour = new Sejour();
        $form = $this->createForm(SejourType::class, $sejour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sejour);
            $entityManager->flush();

            $this->addFlash('success', 'Le séjour a été créé avec succès.');
            return $this->redirectToRoute('app_sejour_index');
        }

        return $this->render('sejour/new.html.twig', [
            'sejour' => $sejour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sejour_show', methods: ['GET'])]
    public function show(Sejour $sejour): Response
    {
        return $this->render('sejour/show.html.twig', [
            'sejour' => $sejour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sejour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sejour $sejour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SejourType::class, $sejour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le séjour a été modifié avec succès.');
            return $this->redirectToRoute('app_sejour_index');
        }

        return $this->render('sejour/edit.html.twig', [
            'sejour' => $sejour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sejour_delete', methods: ['POST'])]
    public function delete(Request $request, Sejour $sejour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sejour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sejour);
            $entityManager->flush();
            $this->addFlash('success', 'Le séjour a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_sejour_index');
    }
} 