<?php

namespace App\Repository;

use App\Entity\CodeUiAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CodeUiAll>
 *
 * @method CodeUiAll|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeUiAll|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeUiAll[]    findAll()
 * @method CodeUiAll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeUiAllRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeUiAll::class);
    }

    public function add(CodeUiAll $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CodeUiAll $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CodeUiAll[] Returns an array of CodeUiAll objects
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

//    public function findOneBySomeField($value): ?CodeUiAll
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
