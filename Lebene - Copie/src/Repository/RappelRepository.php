<?php

namespace App\Repository;

use App\Entity\Rappel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rappel>
 *
 * @method Rappel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rappel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rappel[]    findAll()
 * @method Rappel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RappelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rappel::class);
    }

    public function add(Rappel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Rappel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    public function rappelByFacture(int $id)
    {
        return $this->createQueryBuilder('r')
             ->where('r.facture = :id')
             ->setParameter('id',$id)
             ->getQuery()
             ->getResult()
             ;
       
    }

    /*public function programmeDeLaJournee(Date $aujourdui)
    {
        
        return $this->createQueryBuilder('e')
             
             ->where('e.jourAt = :aujourdui and e.dateFin ')
             ->andWhere('e.statut= :statut')
             ->setParameter('id',$id)
             ->setParameter('statut',$statut)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }*/

//    /**
//     * @return Rappel[] Returns an array of Rappel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rappel
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
