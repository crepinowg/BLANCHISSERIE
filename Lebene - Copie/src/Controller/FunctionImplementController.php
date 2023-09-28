<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FactureRepository;
use App\Repository\DepenseRepository;
use App\Repository\AdministrateurRepository;
use App\Repository\PaiementRepository;
use App\Repository\CleProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class FunctionImplementController extends AbstractController
{

    public function __construct(
        
        FactureRepository $factureRepo,
        PaiementRepository $paiementRepo,
        CleProduitRepository $repoCle, 
        EntityManagerInterface $em,
        AdministrateurRepository $repoAdmin,
        AuthorizationCheckerInterface $authorizationChecker,
        DepenseRepository $depenseRepo){
        $this->depenseRepo = $depenseRepo;
        $this->paiementRepo = $paiementRepo;
        $this->repoAdmin = $repoAdmin;
        $this->repoCle= $repoCle;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->factureRepo=$factureRepo;
        
    } 

    #[Route('/totalGeneral', name: 'app.totalGeneral')]
    public function totalGeneral()
    {
        $facture=$this->factureRepo->findAll();

        $paiement=$this->paiementRepo->findAll();
        $prixPaiement=0;
        foreach ($paiement as $key => $value) {
            $prixPaiement = $prixPaiement + $value->getPaiementFinal();
        }

        $totalGeneral=0;
        $tvaGeneral=0;
        $ttcGeneral=0;
        $nombreReduction=0;
        $nombreRecuperation=0;
        $gainPerdu=0;
            foreach ($facture as $item => $value){
                $reduction=$value->getTauxReduction();
                
                $factureEtat=$value->getEtat();
                
                $tva=$value->getTotalTva();
                $ttc=$value->getTotalTtc();
                if($reduction>0 &&  $factureEtat=='LIVRAISON'){
                    $totalGeneral=$totalGeneral+$tva;
                    $nombreReduction=$nombreReduction+1;
                }
                else if($reduction==0 &&  $factureEtat=='LIVRAISON'){
                    $totalGeneral=$totalGeneral+$ttc;
                }
                else if ( $factureEtat=='ATTENTE') {
                    $nombreRecuperation=$nombreRecuperation+1;
                }


                $tvaGeneral=$tvaGeneral+$tva;
                $ttcGeneral=$ttcGeneral+$ttc;
                $gainPerdu=$ttcGeneral-$tvaGeneral;


            }
        $totalDepenses = $this->totalDepenses();
        //dd($totalDepenses);
        $totalGeneral = $totalGeneral-($totalDepenses+$prixPaiement);

       $data = array(
            "totalGeneral"=>$totalGeneral,
            "nombreReduction"=>$nombreReduction,
            "tvaGeneral"=>$tvaGeneral,
            "ttcGeneral"=>$ttcGeneral,
            "totalDepenses"=>$totalDepenses,
            "nombreRecuperation"=>$nombreRecuperation,
            "gainPerdu"=>$gainPerdu,
        );

        return $data;
    }

    #[Route('/totalDepenses', name: 'app.totalDepenses')]
    public function totalDepenses()
    {
        $factureAll=$this->factureRepo->findAll();
        $depenses=$this->depenseRepo->findAll();

        $totalDepenses=0;
        foreach ($depenses as $key => $value) {
            if($value->isCalculDepense() == false){
                $totalDepenses = $totalDepenses + $value->getPrixTotal();
                $value->setCalculDepense(true);
                $this->em->persist($value);
            }  
        }
        //dd($totalDepenses);
        return $totalDepenses;
    }

    #[Route('/base', name: 'app.base')]
    public function checking()
    {
        $allCle = $this->repoCle->findAll();
    
        foreach ($allCle as $key => $value) {

            
            if($value->isStatut()==1 AND $value->isStatut()==0 AND $value->getDureeReste()>0){

                $date = new DateTime($value->getUseAt());
                $dateToday = new DateTime();
                $diff = $date->diff($dateToday);
                $diffEnJours = $diff->days;
                $dureeFinal = $value->getDuree() - $diffEnJours;
                $value->setDureeReste($dureeFinal);
                $this->em->persist($value);
                if($dureeFinal<=0){
                    $value->setEtat(true);
                    $value->getAdministrateur()->setSuspendu(true);
                    $this->em->persist($value);
                    $this->em->flush();
                    return $this->redirectToRoute('app.security');
                }
            }
           
        }
        $this->em->flush();
      
    }

    #[Route('/gradeClient', name: 'app.gradeClient')]
    public function gradeClient($client,$touteLesFactures)
    {
        foreach ($client as $item => $value){
            $idClient=$value->getId();
           
            $nombreLivraisonParClient=$this->factureRepo->nombreLivraisonParClient($idClient);

            if($touteLesFactures == 0){
                $pourcentageLivraison=0;
            }

            else{
                $pourcentageLivraison=100*$nombreLivraisonParClient/$touteLesFactures;

                if($pourcentageLivraison==0){
                    $value->setStatut('N/A');
                }
                else if($pourcentageLivraison>1 AND $pourcentageLivraison<30){
                $value->setStatut('BLACK');
                }
                else if($pourcentageLivraison>=30 && $pourcentageLivraison<40 ){
                $value->setStatut('SILVER');
                }
                else if($pourcentageLivraison>=40 && $pourcentageLivraison<50 ){
                $value->setStatut('GOLD');
                }
                else if($pourcentageLivraison>=50 ){
                $value->setStatut('DIAMOND');
                }
            }
            $this->em->persist($value);
                  
        }
        //dd('okay');
        $this->em->flush();
    }

    #[Route('/roles_admin_gerant', name: 'app.rolesAdminGerant')]
    public function roles_admin_gerant(Security $security)
    {
        /*if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                 $this->functionImplement->checking();
                
            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }*/
    }

    #[Route('/admin_suspendu', name: 'app.adminSuspendu')]
    public function admin_suspendu()
    {
        $admin = $this->repoAdmin->findAll();
        $response = true;
        foreach ($admin as $key => $value) {
            $suspendu = $value->isSuspendu();
        }
        return $suspendu;
        
    }

    #[Route('/gerant_suspendu', name: 'app.gerantSuspendu')]
    public function gerant_suspendu()
    {
        if($this->getUser()->getGerant() != null){
            $suspendu = $this->getUser()->getGerant()->isStatut();
            return $suspendu;
        }
        elseif($this->getUser()->getGerant() == null){
            $suspendu = "";
            return $suspendu;
            
        }
        if($this->getUser()->getEmploye() != null){
            $suspendu = $this->getUser()->getEmploye()->isStatut();
            return $suspendu;
        }
        elseif($this->getUser()->getEmploye() == null){
            $suspendu = "";
            return $suspendu;
        }
        //$suspendu = $this->getUser()->getGerant()->isStatut();
        
        
        
    }

    #[Route('/employe_suspendu', name: 'app.employeSuspendu')]
    public function employe_suspendu()
    {
        if($this->getUser()->getEmploye() != null){
            $suspendu = $this->getUser()->getEmploye()->isStatut();
        }
        else if($this->getUser()->getEmploye() == null){
            $suspendu = "";
        }
        //$suspendu = $this->getUser()->getGerant()->isStatut();
        
        return $suspendu;
        
    }
}
