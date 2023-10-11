<?php

namespace App\Repository;

use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notifications>
 *
 * @method Notifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifications[]    findAll()
 * @method Notifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifications::class);
    }

    public function add(Notifications $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notifications $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMarkView($id): array
    {
        $query= $this->createQueryBuilder('n')
            ->where('n.id = :id ')
            ->setParameter('id', $id)
            
        ;
        return $query->getQuery()->getResult();
    }

    public function findAllOrder()
    {
        $query= $this->createQueryBuilder('n')
            ->where('n.reader = 0')
            ->orderBy('n.id', 'DESC')
            
        ;
        return $query->getQuery()->getResult();
    }
    
    public function countClient()
    
    {
        $null = "null";
        return $this->createQueryBuilder('n')
             ->select("COUNT(n.id)")
             ->where("n.client != :null and n.reader = 0")
             ->setParameter('null', $null)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }

    public function countFactures()
    
    {
        $null = "null";
        return $this->createQueryBuilder('n')
             ->select("COUNT(n.id)")
             ->where("n.facture != :null and n.reader = 0")
             ->setParameter('null', $null)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }

    public function countDepense()
    
    {
        $null = "null";
        return $this->createQueryBuilder('n')
             ->select("COUNT(n.id)")
             ->where("n.depense != :null and n.reader = 0")
             ->setParameter('null', $null)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }

    public function countEmploye()
    
    {
        $null = "null";
        return $this->createQueryBuilder('n')
             ->select("COUNT(n.id)")
             ->where("n.employe != :null and n.reader = 0")
             ->setParameter('null', $null)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }

//    /**
//     * @return Notifications[] Returns an array of Notifications objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notifications
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
