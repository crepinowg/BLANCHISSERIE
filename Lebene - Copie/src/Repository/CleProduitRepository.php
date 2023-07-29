<?php

namespace App\Repository;

use App\Entity\CleProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CleProduit>
 *
 * @method CleProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method CleProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method CleProduit[]    findAll()
 * @method CleProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CleProduit::class);
    }

    public function add(CleProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CleProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByAdmin(int $id): array
    {
       return $this->createQueryBuilder('c')
            ->where('c.administrateur = :id')
            ->andWhere('c.statut = 0')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
       ;
    }

//    /**
//     * @return CleProduit[] Returns an array of CleProduit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CleProduit
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
