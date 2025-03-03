<?php

namespace App\Repository;

use App\Entity\DossierMedicale;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DossierMedicale>
 *
 * @method DossierMedicale|null find($id, $lockMode = null, $lockVersion = null)
 * @method DossierMedicale|null findOneBy(array $criteria, array $orderBy = null)
 * @method DossierMedicale[]    findAll()
 * @method DossierMedicale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DossierMedicaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierMedicale::class);
    }

    public function save(DossierMedicale $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DossierMedicale $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Search for dossiers by query string
     */
    public function searchByQuery(User $patient, string $query): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.patient = :patient')
            ->andWhere('d.id LIKE :query OR d.statutDossier LIKE :query OR d.historiqueDesMaladies LIKE :query OR d.operationsPassees LIKE :query OR d.consultationsPassees LIKE :query OR d.notes LIKE :query')
            ->setParameter('patient', $patient)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('d.dateDeCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 