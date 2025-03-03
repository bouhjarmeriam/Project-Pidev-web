<?php

namespace App\Repository;

use App\Entity\Sejour;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sejour>
 *
 * @method Sejour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sejour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sejour[]    findAll()
 * @method Sejour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SejourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sejour::class);
    }

    public function save(Sejour $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sejour $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all stays for a given patient
     */
    public function findByPatient(User $patient): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.dossierMedicale', 'd')
            ->where('d.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('s.dateEntree', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Search for sejours by query string
     */
    public function searchByQuery(User $patient, string $query): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.dossierMedicale', 'd')
            ->where('d.patient = :patient')
            ->andWhere('s.id LIKE :query OR s.typeSejour LIKE :query OR s.moyenPaiement LIKE :query OR s.statutPaiement LIKE :query')
            ->setParameter('patient', $patient)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('s.dateEntree', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 