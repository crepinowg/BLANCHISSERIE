<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Entity\Livraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function add(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /*public function jointureTable(int $entete_id){

        $em = $this->getEntityManager();
        $query = $em->createQuery(

            'SELECT f, e, c
             FROM App\Entity\Facture f
             INNER JOIN f.entete e
             INNER JOIN f.client c
             WHERE f.client = :id'
        )->setParameter('id',$entete_id);

        return $query->getResult();

    }*/

    public function jointureTable(int $id, int $page,int $limit){

        //dd($limit);
        $query = $this->createQueryBuilder('f')
                ->where('f.client = :id')
                ->setFirstResult(($page-1)*$limit)
                
                ->leftJoin('f.entete','e')
                ->setParameter('id',$id)
                ->orderBy('f.id','DESC')
                ;
            

        return $query->getQuery()->getResult();

    }

    public function factureLivrer()
    {
        $etat='LIVRAISON';
        return $this->createQueryBuilder('f')
             ->select("COUNT(f.id)")
             ->where('f.etat = :etat')
             ->setParameter('etat',$etat)
             ->getQuery()
             ->getSingleScalarResult()
             ;
       
    }

    public function factureNonLivrer()
    {
        $etat='LIVRAISON';
        return $this->createQueryBuilder('f')
             ->where('f.etat != :etat')
             ->setParameter('etat',$etat)
             ->getQuery()
             ->getResult()
             ;
       
    }

    public function findClientViaFacture(int $id)
    {
        return $this->createQueryBuilder('f')
             ->where('f.id = :id')
             ->setParameter('id',$id)
             ->getQuery()
             ->getResult()
             ;
       
    }

    public function findFacture(int $id){

        $query = $this->createQueryBuilder('f')
                ->select('f.id')
                ->where('f.client = :id')
                ->setParameter('id',$id)
                ;
            

        return $query->getQuery()->getResult();

    }

    public function thisFacture(int $id){

        $query = $this->createQueryBuilder('f')
                ->where('f.id = :id')
                ->setParameter('id',$id)
                ;
            

        return $query->getQuery()->getResult();

    }

    public function fatureLivraison(Livraison $livraison){

        $query = $this->createQueryBuilder('f')
                ->select('f.id')
                ->where('f.livraison = :livraison')
                ->setParameter('livraison',$livraison)
                ;
            

        return $query->getQuery()->getResult();

    }

    public function factureClientId(int $id)
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.client = :id')
         ->andWhere('e.etat= :statut')
         ->setParameter('id',$id)
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function allFacture()
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->where('e.etat = :statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

public function nbreLivraison2()
{
    $statut='livraison';
    return $this->createQueryBuilder('e')
         ->select("COUNT(e.id)")
         ->andWhere('e.etat= :statut')
         ->setParameter('statut',$statut)
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}

     public function listeFacture(int $id)
     {
        $query = $this->createQueryBuilder('f')
                ->where('f.client = :id')
                ->orderBy('f.id','DESC')
                ->leftJoin('f.livraison','l')
                ->setParameter('id',$id)

                ;
            

        return $query->getQuery()->getResult();

    }

    public function gainFacture(int $id)
     {
        $statut='livraison';
        $query = $this->createQueryBuilder('f')
                ->where('f.client = :id')
                ->orderBy('f.id','DESC')
                ->leftJoin('f.livraison','l')
                ->andWhere('l.statut = :statut')
                ->setParameter('id',$id)
                ->setParameter('statut',$statut)

                ;
            

        return $query->getQuery()->getResult();

    }

    public function findFact()
     {
        $value='LIVRAISON';
        $query = $this->createQueryBuilder('f')
                ->where('f.etat = :value')
                ->setParameter('value',$value)
                ->orderBy('f.id','DESC')
                ;
            

        return $query->getQuery()->getResult();

    }

      public function allDesFact()
     {

        $query = $this->createQueryBuilder('f')
                ->orderBy('f.id','DESC')
                ->leftJoin('f.livraison','l')
                ;
            

        return $query->getQuery()->getResult();

    }

    public function nombreLivraisonParClient(int $id)
     {
        $value='LIVRAISON';
        $query = $this->createQueryBuilder('f')
                 ->select("COUNT(f.id)")
                ->where('f.client = :id')
                ->andWhere('f.etat = :value')
                ->setParameter('value',$value)
                ->setParameter('id',$id)
                ->orderBy('f.id','DESC')
                ;
            

        return $query->getQuery()->getSingleScalarResult();

    }

    

    public function countFacture(int $id)
{
    return $this->createQueryBuilder('f')
         ->select("COUNT(f.id)")
         ->where('f.client= :id')
         ->setParameter('id',$id)
         ->getQuery()
         ->getSingleScalarResult()
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

public function countNumeroFacture()
{
    return $this->createQueryBuilder('c')
         ->select("COUNT(c.id)")
         ->getQuery()
         ->getSingleScalarResult()
         ;
   
}



      /*  public function countClient()
{
    $qb = $this->createQueryBuilder('e')
            ->select($qb->expr()->count('e'))
            ->where()
            ;
 
    return (int) $qb->getQuery()->getSingleScalarResult();
}

//    /**
//     * @return Facture[] Returns an array of Facture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Facture
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
