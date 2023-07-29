<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function add(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDataClient(): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.id >= 1')
            ->getQuery()
            ->getResult()
       ;
    }

    public function findDataGerant(): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.id >= 1')
            ->getQuery()
            ->getResult()
       ;
    }
    public function findDataEmploye(): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.id >= 1')
            ->getQuery()
            ->getResult()
       ;
    }

    public function findClientById(int $id): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.client = :id')
            ->setParameter("id",$id)
            ->getQuery()
            ->getResult()
       ;
    }

    public function findGerantById(int $id): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.gerant = :id')
            ->setParameter("id",$id)
            ->getQuery()
            ->getResult()
       ;
    }

    public function findEmployeById(int $id): array
    {
       return $this->createQueryBuilder('u')
            ->andWhere('u.employe = :id')
            ->setParameter("id",$id)
            ->getQuery()
            ->getResult()
       ;
    }


//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
