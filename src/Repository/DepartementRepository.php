<?php

namespace App\Repository;

use App\Entity\Departement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Departement>
 */
class DepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departement::class);
    }

    //    /**
    //     * @return Departement[] Returns an array of Departement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    public function findBySearchAndSort(string $search = '', string $sortBy = 'nom', string $sortOrder = 'asc'): array
{
    $queryBuilder = $this->createQueryBuilder('d');

    // Ajouter une condition de recherche si un terme de recherche est fourni
    if ($search) {
        $queryBuilder->andWhere('d.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    // Ajouter le tri
    $queryBuilder->orderBy('d.' . $sortBy, $sortOrder);

    return $queryBuilder->getQuery()->getResult();
}

    //    public function findOneBySomeField($value): ?Departement
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
