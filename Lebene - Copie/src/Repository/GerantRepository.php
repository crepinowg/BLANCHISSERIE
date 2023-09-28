<?php

namespace App\Repository;

use App\Entity\Gerant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gerant>
 *
 * @method Gerant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gerant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gerant[]    findAll()
 * @method Gerant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GerantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gerant::class);
    }

    public function add(Gerant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Gerant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listeGerant(int $id)
    {
       $query = $this->createQueryBuilder('g')
               ->where('g.id = :id')
               ->orderBy('g.id','DESC')
               ->setParameter('id',$id)

               ;
           

       return $query->getQuery()->getResult();

   }

   public function gerantActif(int $id)
    {
        $null="NULL";
       $query = $this->createQueryBuilder('g')
               ->where('g.id = :id')
               ->andWhere('g.statut = 0')
               ->andWhere('g.statut = :null')
               ->orderBy('g.id','DESC')
               ->setParameter('id',$id)
               ->setParameter('null',$null)

               ;
           

       return $query->getQuery()->getResult();

   }

//    /**
//     * @return Gerant[] Returns an array of Gerant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Gerant
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
