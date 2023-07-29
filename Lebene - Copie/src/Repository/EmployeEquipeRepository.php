<?php

namespace App\Repository;

use App\Entity\EmployeEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmployeEquipe>
 *
 * @method EmployeEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmployeEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmployeEquipe[]    findAll()
 * @method EmployeEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeEquipe::class);
    }

    public function add(EmployeEquipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmployeEquipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function employeViaEmployeEquipe(int $id): array
    {
       return $this->createQueryBuilder('e')
            ->andWhere('e.equipe = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult()
       ;
    }

//    /**
//     * @return EmployeEquipe[] Returns an array of EmployeEquipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmployeEquipe
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
