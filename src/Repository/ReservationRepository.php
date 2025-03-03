<?php
namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findConflictingReservations(Salle $salle, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.salle = :salle')
            ->andWhere('r.dateDebut < :end AND r.dateFin > :start')
            ->setParameter('salle', $salle)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}