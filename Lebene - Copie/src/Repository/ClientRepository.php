<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function add(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     public function findClient(): array
    {
       return $this->createQueryBuilder('c')
            ->andWhere('c.id >= 1')
           // ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
       ;
    }
    

    
       public function countClient()
{
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function countB()
{
    $statut='BLACK';
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->where('c.statut=:statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}
public function countS()
{
    $statut='SILVER';
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->where('c.statut=:statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}
public function countG()
{
    $statut='GOLD';
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->where('c.statut=:statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}
public function countD()
{
    $statut='DIAMOND';
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->where('c.statut=:statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function countN()
{
    $statut='N/A';
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->where('c.statut=:statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

  public function findBest()
    {
        $statut = "DIAMOND";
       return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
       ;
    }

    public function findBest2()
    {
        $statut = "DIAMOND";
       return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')  
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getResult()
       ;
    }

    




    

//    /**
//     * @return Client[] Returns an array of Client objects
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

//    public function findOneBySomeField($value): ?Client
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
