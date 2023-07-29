<?php

namespace App\Repository;

use App\Entity\Livraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livraison>
 *
 * @method Livraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livraison[]    findAll()
 * @method Livraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livraison::class);
    }

    public function add(Livraison $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livraison $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     public function findClient($id,$page,$limit): array
    {
        $query= $this->createQueryBuilder('l')
            ->andWhere('l.client = :id')
            ->setFirstResult(($page-1)*$limit)
            ->setMaxResults($limit)
            ->setParameter('id', $id)
            ->orderBy('l.id','DESC')
            
        ;
        return $query->getQuery()->getResult();
    }

     public function nbreLivraison(int $id)
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.client = :id')
         ->andWhere('e.statut= :statut')
         ->setParameter('id',$id)
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

/* public function totalGain()
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("e.")
         ->where('e.client = :id')
         ->andWhere('e.statut= :statut')
         ->setParameter('id',$id)
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}*/

 public function nbreLivraison2()
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->andWhere('e.statut= :statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function goldL(int $id)
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.client=:id')
         ->andWhere('e.statut= :statut')
         ->setParameter('statut',$statut)
         ->setParameter('id',$id)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}
 public function allLivraison()
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.statut = :statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}
//    /**
//     * @return Livraison[] Returns an array of Livraison objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livraison
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
