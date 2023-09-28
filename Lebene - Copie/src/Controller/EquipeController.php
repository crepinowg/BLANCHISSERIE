<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipe;
use App\Entity\Notifications;
use Symfony\Component\Security\Core\Security;

use App\Repository\EquipeRepository;
use App\Repository\EmployeRepository;
use App\Controller\FunctionImplementController;
use App\Repository\FactureEquipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\Autowire\Autowire;

class EquipeController extends AbstractController
{

     /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct( 
        EquipeRepository $equipeRepo, 
        FactureEquipeRepository $factureEquipeRepo,
        FunctionImplementController $functionImplement, 
        EmployeRepository $employeRepo, 
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        ){
        
        $this->em = $em;
        $this->equipeRepo = $equipeRepo;
        $this->factureEquipeRepo = $factureEquipeRepo;
        $this->employeRepo = $employeRepo;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('liste_equipe', name: 'app.equipe.liste')]
    public function index(Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{       
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                $statut = $this->functionImplement->gerant_suspendu();
                
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }
                if ($statut == 1){
                    return $this->redirectToRoute('app.logout');
                }
                
            }
            else{
                return $this->redirectToRoute('app.notfound'); 
            }
        }
        $equipe = $this->equipeRepo->findAll();
        return $this->render('liste_equipe.html.twig', [
            'controller_name' => 'EquipeController',
            "equipe"=>$equipe
        ]);
    }

    #[Route('suivi_equipe', name: 'app.equipe.suivi')]
    public function suivi(Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{       
            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            
            if ($suspendu == 1){
                return $this->redirectToRoute('app.security');
            }
            if ($statut == 1){
                return $this->redirectToRoute('app.logout');
            }
        }
        

        $find_factureEquipe_all = $this->factureEquipeRepo->findAll();
        $equipe = $this->equipeRepo->findAll();
        return $this->render('suivi_equipe.html.twig', [
            'controller_name' => 'EquipeController',
            "equipe"=>$equipe,
            "find_factureEquipe_all"=>$find_factureEquipe_all
        ]);
    }


    #[Route('ajout_equipe', name: 'app.equipe.ajout')]
    public function ajout(Security $security): Response
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
        return $this->render('ajout_equipe.html.twig', [
            'employe'=>$employe
        ]);
    }

    #[Route('suspendre_equipe-{id}', name: 'app.equipe.suspendre')]
    public function suspendre_equipe(Request $request, Equipe $equipe,Security $security, int $id): Response
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
        if($equipe->isStatut()==0){
            $equipe->setStatut(true);
        }
        else if($equipe->isStatut()==1){
            $equipe->setStatut(false);
        }
        $this->em->persist($equipe);
        $this->em->flush();
        return $this->redirectToRoute('app.equipe.liste');
    }

    
}
