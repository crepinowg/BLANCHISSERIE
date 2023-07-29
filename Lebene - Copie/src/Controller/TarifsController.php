<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tarifs;
use Symfony\Component\Security\Core\Security;
use App\Repository\TarifsRepository;
use App\Repository\IconsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\FunctionImplementController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;
use App\Doctrine\ForeignKeyManager;

class TarifsController extends AbstractController
{
    public function __construct( 
        TarifsRepository $tarifsRepo, 
        ForeignKeyManager $foreignKeyManager,
        FunctionImplementController $functionImplement,
        EntityManagerInterface $em,
        IconsRepository $iconsRepo,
        AuthorizationCheckerInterface $authorizationChecker
        ){
        
        $this->em = $em;
        $this->foreignKeyManager = $foreignKeyManager;
        $this->tarifsRepo = $tarifsRepo;
        $this->functionImplement = $functionImplement;
        $this->iconsRepo = $iconsRepo;
        $this->authorizationChecker = $authorizationChecker;
    }
    #[Route('/tarifs_ajout', name: 'app.tarifs.ajout')]
    public function ajout(Request $request,Security $security,): Response
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

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

            $icons = $this->iconsRepo->findAll();
            $tarifsAll = $this->tarifsRepo->findAll();
            if ($request->isXmlHttpRequest()) {
                foreach ($icons as $key => $value) {

                    $tarifs = "tarifs".$key+1;
                    $tarifs = new Tarifs;
                    
                    $iconsObjet=$request->request->get('iconsObjet'.$key+1);
                    $prix=$request->request->get('prix');
                    $type=$request->request->get('type');
                    $express=$request->request->get('express');

                    if($prix==null OR $type== null){
                        $message="Veuillez remplir tout les champs";            
                        return $this->json($message);
                    }
                    if($prix<50 ){
                        $message="Le prix doit être supérieur ou égale a 50F";            
                        return $this->json($message);
                    }
                    
                    
                    if (isset($prix,$type)) {
                        if(isset($iconsObjet)){
                        
                            
                           
                            if($iconsObjet == true){
                                foreach ($tarifsAll as $key => $values) {
                                    
                                    if ($values->getIcons()== $value AND $values->getType()==$type AND $values->isExpress()==$express AND $values->isStatut()==1){
                                        $message="Le tarif  '".strtoupper($values->getIcons()->getNomIcon())."'  a été préalablement enregistrer. Veuillez la modifier";            
                                        return $this->json($message);
                                    }
                                }

                                $tarifs->setIcons($value);
                            }

                            if($type == true){
                                $tarifs->setType($type);
                            }

                            $tarifs->setPrix($prix);
                            

                            if($checkUser == false){
                                $tarifs->setGerant($gerantUser);
                            }
                
                            else if($checkUser == true){
                                $tarifs->setAdmin($admin);
                            }

                            if($express == false){
                                
                                $tarifs->setExpress(false);
                            }
                            elseif($express == true){
                                
                                $tarifs->setExpress(true);
                            }
                            
                            $this->em->persist($tarifs);
                            
                        }
                    }
                }
                
                
                $this->em->flush();
                return new JsonResponse([
                    'success' => true,
                    'redirect_url' => $this->generateUrl('app.tarifs.liste'),
                ]);
            }
            else{
                $icons = $this->iconsRepo->findAll();
                $iconsJson = json_encode($icons);
                return $this->render('forms-layouts.html.twig', [
                'controller_name' => 'TarifsController',
                'iconsJson'=>$iconsJson,
                'icons'=>$icons
                ]);
            }
        
    }


    #[Route('/tarifs_update-{id}', name: 'app.tarifs.update')]
    public function tarifs_update(Request $request,Security $security,Tarifs $tarifss,int $id, Connection $connection): Response
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


        $tarif = new Tarifs;
        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

            $icons = $this->iconsRepo->findAll();
            $tarifsAll = $this->tarifsRepo->findAll();
            if ($request->isXmlHttpRequest()) {
                $tarifss->setStatut(false);
               // $this->foreignKeyManager->disableForeignKeys();
                foreach ($icons as $key => $value) {

                    //$tarifs = "tarifs".$key+1;
                    
                    $iconsObjet=$request->request->get('iconsObjet'.$key+1);
                    $prix=$request->request->get('prix');
                    $type=$request->request->get('type');
                    $express=$request->request->get('express');

                    if($prix==null OR $type== null){
                        $message="Veuillez remplir tout les champs";            
                        return $this->json($message);
                    }

                    if($prix<50 ){
                        $message=" Le prix doit être supérieur ou égale a 50F ";            
                        return $this->json($message);
                    }
                    
                    
                    if (isset($prix,$type)) {
                        if(isset($iconsObjet)){

                            if($iconsObjet == true){
                                foreach ($tarifsAll as $key => $values) {
                                    
                                    if ($values->getIcons()== $value AND $values->getType()==$type AND $values->isExpress()==$express AND $values->getId()!=$id AND $values->isStatut()==1){
                                        $message="Le tarif  '".strtoupper($values->getIcons()->getNomIcon())."' a été préalablement enregistrer. Veuillez la modifier";            
                                        return $this->json($message);
                                    }
                                }
                                $tarif->setIcons($value);
                            }

                                
                                if($type == true){
                                    $tarif->setType($type);
                                }
    
                                $tarif->setPrix($prix);
                                
    
                                if($checkUser == false){
                                    $tarif->setGerant($gerantUser);
                                }
                    
                                else if($checkUser == true){
                                    $tarif->setAdmin($admin);
                                }
    
                                if($express == false){
                                    
                                    $tarif->setExpress(false);
                                }
                                elseif($express == true){
                                    
                                    $tarif->setExpress(true);
                                }
                                
                                $this->em->persist($tarif);
    
                        }
                    }
                }
                               
                $this->em->flush();
                //$this->foreignKeyManager->enableForeignKeys();
                return new JsonResponse([
                    'success' => true,
                    'redirect_url' => $this->generateUrl('app.tarifs.liste'),
                ]);
            }
            else{
                $icons = $this->iconsRepo->findAll();
                $iconsJson = json_encode($icons);
                $tarifs=$this->tarifsRepo->find($id);
                return $this->render('update_tarifs.html.twig', [
                'controller_name' => 'TarifsController',
                'iconsJson'=>$iconsJson,
                'icons'=>$icons,
                "tarifs"=>$tarifs
                ]);
            }
    }

    #[Route('/tarifs_liste', name: 'app.tarifs.liste')]
    public function liste(Security $security,): Response
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
        $tarifs = $this->tarifsRepo->findByStatut();
        dump($tarifs);
        //$this->addFlash('NoClient','Aucun client enregistrer. Ajouter  pour commencer');
        return $this->render('liste-tarifs.html.twig', [
            'controller_name' => 'ClientController',
            'tarifs'=>$tarifs
        ]);
    }

    #[Route('/supprimer_tarif-{id}', name: 'app.tarifs.supprimer')]
    public function supprimer_tarif(Tarifs $tarif, Security $security, int $id): Response
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
        $tarif->setStatut(false);
        $this->em->persist($tarif);
        $this->em->flush();
        return $this->redirectToRoute('app.tarifs.liste');
       
    }

}
