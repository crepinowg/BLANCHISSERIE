<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeRepository;
use App\Repository\GerantRepository;
use Symfony\Component\Security\Core\Security;

use App\Repository\PaiementRepository;
use App\Entity\Employe;
use App\Entity\Gerant;
use App\Controller\FunctionImplementController;
use App\Entity\Paiement;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PaiementController extends AbstractController
{

    public function __construct(
        EmployeRepository $employeRepo ,
        FunctionImplementController $functionImplement,
        GerantRepository $gerantRepo ,
        PaiementRepository $paiementRepo ,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
    ){
        $this->em = $em;
        $this->employeRepo = $employeRepo;
        $this->gerantRepo = $gerantRepo;
        $this->paiementRepo = $paiementRepo;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
    }
    #[Route('/paiement', name: 'app.paiement')]
    public function index(Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }
        $employe = $this->employeRepo->findAll();
        $gerant = $this->gerantRepo->findAll();
        return $this->render('liste_paiement.html.twig', [
            'controller_name' => 'PaiementController',
            'employe'=>$employe,
            'gerant'=>$gerant
        ]);
    }

    #[Route('/effectuer_paiement-{id}', name: 'app.effectuerPaiement')]
    public function effectuer(Request $request, int $id,Security $security): Response
    {   
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }
        return $this->render('ajout_paiement.html.twig', [
            'controller_name' => 'PaiementController',
            'id'=>$id
        ]);
    }

    #[Route('/effectuer_paiementGerant-{id}', name: 'app.effectuerPaiementGerant')]
    public function effectuer_paiementGerant(Request $request, int $id,Security $security): Response
    {   
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }
        return $this->render('ajout_paiement_gerant.html.twig', [
            'controller_name' => 'PaiementController',
            'id'=>$id
        ]);
    }

    #[Route('/effectuerPaiement-{id}', name: 'app.employe.paiement')]
    public function paiement(Request $request,Employe $employe,int $id,Security $security): Response
    {   
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $paiement = new Paiement;
        $notifications = new Notifications;
        
        

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }
        
        (int)$augmentation=$request->request->get('augmentation');
        $moyen=$request->request->get('moyen');
        $createdAt=Date('Y-m-d h:i');
        $employeId = $this->employeRepo->findEmploye($id);
        //dd($moyen."-".$augmentation);
        if (isset($moyen)){
            
            $paiement->setMoyenPaiement($moyen);
            $paiement->setPaiementAt($createdAt);
            if($augmentation<0){
                $paiement->setAugmentation(0);
                foreach ($employeId as $key => $value) {
                    $paiement->setPaiementFinal($value->getSalaire());
                }
                
            }
            elseif($augmentation==0){
                $paiement->setAugmentation(0);
                foreach ($employeId as $key => $value) {
                    $paiement->setPaiementFinal($value->getSalaire());
                    $paiement->setSalaireInitital($value->getSalaire());
                }
            }
            elseif($augmentation>0){
                
                $paiement->setAugmentation($augmentation);
                foreach ($employeId as $key => $value) {
                    $calculPaiementFinal = $value->getSalaire()*$augmentation/100;
                    $paiementFinal = $calculPaiementFinal + $value->getSalaire();
                    $paiement->setSalaireInitital($value->getSalaire());
                }
                $paiement->setPaiementFinal($paiementFinal);
            }

          

            else if($checkUser == true){
                $paiement->setAdministrateur($admin);
                
            }
            $paiement->setEmploye($employe);
            $this->em->persist($paiement);

            $notifications->setTitre("Mise à jour Paiement");
            $notifications->setReader(false);
            $notifications->setCreatedAt(date("Y-m-d h:i"));
            $notifications->setTypeNotif("MJ");

           
            if($checkUser == true){
                $notifications->setAdmin($admin);
            }

            $notifications->setPaiement($paiement);
            $this->em->persist($notifications);
            
        }

        //dd($moyen."-".$augmentation);

        $this->em->flush();
        
        return $this->redirectToRoute('app.tabs.paiement');
    }

    #[Route('/effectuerPaiementGerant-{id}', name: 'app.employe.paiementGerant')]
    public function paiementGerant(Request $request,Gerant $gerant,int $id,Security $security): Response
    {   
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $paiement = new Paiement;
        $notifications = new Notifications;
        
        

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }
        
        (int)$augmentation=$request->request->get('augmentation');
        $moyen=$request->request->get('moyen');
        $createdAt=Date('Y-m-d h:i');
        $employeId = $this->gerantRepo->gerantActif($id);
        //dd($moyen."-".$augmentation);
        if (isset($moyen)){
            
            $paiement->setMoyenPaiement($moyen);
            $paiement->setPaiementAt($createdAt);
            if($augmentation<0){
                $paiement->setAugmentation(0);
                foreach ($employeId as $key => $value) {
                    $paiement->setPaiementFinal($value->getSalaire());
                }
                
            }
            elseif($augmentation==0){
                $paiement->setAugmentation(0);
                foreach ($employeId as $key => $value) {
                    $paiement->setPaiementFinal($value->getSalaire());
                    $paiement->setSalaireInitital($value->getSalaire());
                }
            }
            elseif($augmentation>0){
                
                $paiement->setAugmentation($augmentation);
                foreach ($employeId as $key => $value) {
                    $calculPaiementFinal = $value->getSalaire()*$augmentation/100;
                    $paiementFinal = $calculPaiementFinal + $value->getSalaire();
                    $paiement->setSalaireInitital($value->getSalaire());
                }
                $paiement->setPaiementFinal($paiementFinal);
            }

          

            else if($checkUser == true){
                $paiement->setAdministrateur($admin);
                
            }
            $paiement->setGerant($gerant);
            $this->em->persist($paiement);

            $notifications->setTitre("Mise à jour Paiement");
            $notifications->setReader(false);
            $notifications->setCreatedAt(date("Y-m-d h:i"));
            $notifications->setTypeNotif("MJ");

           
            if($checkUser == true){
                $notifications->setAdmin($admin);
            }

            $notifications->setPaiement($paiement);
            $this->em->persist($notifications);
            
        }

        //dd($moyen."-".$augmentation);

        $this->em->flush();
        
        return $this->redirectToRoute('app.tabs.paiement');
    }

    #[Route('/tabs_paiement', name: 'app.tabs.paiement')]
    public function tabsPaiement(Security $security): Response
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $paiement = $this->paiementRepo->findAll();
        return $this->render('liste_paiement_tabs.html.twig', [
            'controller_name' => 'PaiementController',
            'paiement'=>$paiement
        ]);
    }
}
