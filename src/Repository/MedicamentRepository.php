<?php

namespace App\Repository;

use App\Entity\Medicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class MedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicament::class);
    }

    /**
     * Search and filter medications based on name and sorting order
     */
    public function findByFiltersQuery(?string $searchTerm = '', string $sortField = 'm.nom_medicament', string $sortOrder = 'asc'): Query
{
    $qb = $this->createQueryBuilder('m');

    if (!empty($searchTerm)) {
        $qb->andWhere('m.nom_medicament LIKE :search OR m.description_medicament LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');
    }

    // Ensure the sort field is valid to prevent SQL injection
    $allowedFields = ['m.nom_medicament', 'm.date_expiration'];
    if (!in_array($sortField, $allowedFields)) {
        $sortField = 'm.nom_medicament';
    }

    $qb->orderBy($sortField, $sortOrder);

    return $qb->getQuery();
}

}
