<?php
namespace App\Repository;

use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

    /**
     * Récupérer les salles disponibles
     */
    public function findAvailableRooms(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', 'Disponible')
            ->orderBy('s.priorite', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Rechercher une salle avec AJAX
     */
   // Dans SalleRepository.php
public function searchRooms(string $search, string $sortBy = 'nom', string $sortOrder = 'asc'): array
{
    return $this->createQueryBuilder('s')
        ->where('s.nom LIKE :search OR s.type_salle LIKE :search')
        ->setParameter('search', "%$search%")
        ->orderBy('s.' . $sortBy, $sortOrder)
        ->getQuery()
        ->getResult();
}
}
