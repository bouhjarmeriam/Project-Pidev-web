<?php

namespace App\Repository;

use App\Entity\Etage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etage>
 */
class EtageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etage::class);
    }

    /**
     * Find étages by search term and sort them.
     *
     * @param string $search The search term (optional).
     * @param string $sortBy The field to sort by (e.g., 'numero', 'departement.nom').
     * @param string $sortOrder The sort order ('asc' or 'desc').
     * @return Etage[] Returns an array of Etage objects.
     */
    public function findBySearchAndSort(string $search = '', string $sortBy = 'Numero', string $sortOrder = 'asc'): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        // Join the 'departement' table for sorting by department name
        $queryBuilder->leftJoin('e.departement', 'd');

        // Add search conditions
        if ($search) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('e.Numero', ':search'),
                    $queryBuilder->expr()->like('d.nom', ':search')
                )
            )
            ->setParameter('search', '%' . $search . '%');
        }

        // Add sorting
        if ($sortBy === 'departement') {
            $queryBuilder->orderBy('d.nom', $sortOrder);
        } else {
            $queryBuilder->orderBy('e.' . $sortBy, $sortOrder);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}