<?php

namespace App\Repository;

use App\Entity\Entete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entete>
 *
 * @method Entete|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entete|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entete[]    findAll()
 * @method Entete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnteteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entete::class);
    }

    public function add(Entete $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Entete $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function jointureTable(int $entete_id){

        $em = $this->getEntityManager();
        $query = $em->createQuery(

            'SELECT e, f
             FROM App\Entity\Entete e
             INNER JOIN e.facture f
             WHERE e.id = :id'
        )->setParameter('id',$entete_id);

        return $query->getOneOrNullResult();

    }

        public function findEntete(int $id)
{
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.facture = :id')
         ->setParameter('id',$id)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function findEntete2(int $id)
{
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.facture = :id and e.expressDelivered = 1')
         ->setParameter('id',$id)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function findEnteteById(int $id)
{
    return $this->createQueryBuilder('e')
         ->where('e.id = :id')
         ->setParameter('id',$id)
         ->getQuery()
         ->getResult();
   
}

public function findEnteteByFacture(int $id)
{
    return $this->createQueryBuilder('e')
         ->where('e.facture = :id')
         ->setParameter('id',$id)
         ->getQuery()
         ->getResult();
   
}

//    /**
//     * @return Entete[] Returns an array of Entete objects
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

//    public function findOneBySomeField($value): ?Entete
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
