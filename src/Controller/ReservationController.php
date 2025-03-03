<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;
use App\Repository\SalleRepository;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }
    #[Route('/reservation/new/{salleId}', name: 'reservation_new')]
public function new(
    Request $request, 
    EntityManagerInterface $entityManager, 
    SalleRepository $salleRepository, 
    ReservationRepository $reservationRepository, 
    int $salleId
): Response {
    $salle = $salleRepository->find($salleId);
    if (!$salle) {
        throw $this->createNotFoundException('Salle non trouvée.');
    }

    // Vérification de la disponibilité avant réservation
    if ($salle->getStatus() !== 'Disponible') {
        $this->addFlash('error', 'Cette salle est déjà réservée ou en maintenance.');
        return $this->redirectToRoute('salle_index');
    }

    $reservation = new Reservation();
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $dateDebut = $reservation->getDateDebut();
        $dateFin = $reservation->getDateFin();

        // Vérifier les conflits de réservation
        $conflictingReservations = $reservationRepository->findConflictingReservations($salle, $dateDebut, $dateFin);
        if (!empty($conflictingReservations)) {
            $this->addFlash('error', 'La salle est déjà réservée pendant cette période.');
            return $this->redirectToRoute('reservation_new', ['salleId' => $salleId]);
        }

        $reservation->setSalle($salle);

        // Mise à jour du statut de la salle
        $salle->setStatus('Occupé');

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation effectuée avec succès !');

        return $this->redirectToRoute('salle_index');
    }

    return $this->render('reservation/new.html.twig', [
        'form' => $form->createView(),
        'salle' => $salle,
    ]);
}
#[Route('/reservation/list', name: 'reservation_list')]
public function list(ReservationRepository $reservationRepository): Response
{
    $reservations = $reservationRepository->findAll();

    return $this->render('reservation/list.html.twig', [
        'reservations' => $reservations,
    ]);
}
#[Route('/calendrier', name: 'reservation_calendrier')]
public function calendrier(ReservationRepository $reservationRepository): Response
{
    $reservations = $reservationRepository->findAll();

    $events = [];
    foreach ($reservations as $reservation) {
        $events[] = [
            'id' => $reservation->getId(),
            'title' => 'Réservé',
            'start' => $reservation->getDateDebut()->format('Y-m-d H:i:s'),
            'end' => $reservation->getDateFin()->format('Y-m-d H:i:s'),
        ];
    }

    return $this->render('reservation/calendrier.html.twig', [
        'events' => json_encode($events),
    ]);
}
    
}
