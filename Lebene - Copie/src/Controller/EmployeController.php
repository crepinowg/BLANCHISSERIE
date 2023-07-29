<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Security;
use App\Controller\FunctionImplementController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmployeController extends AbstractController
{

    public function __construct(
        EmployeRepository $employeRepo , 
        UtilisateurRepository $utilisateurRepo , 
        AuthorizationCheckerInterface $authorizationChecker,
        FunctionImplementController $functionImplement,
        EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->employeRepo = $employeRepo;
        $this->utilisateurRepo = $utilisateurRepo;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
    }

    
    #[Route("/dataEmploye", name: 'app.data.employe')]
    public function sendDataEmploye()
    {

        $employe = $this->utilisateurRepo->findDataEmploye();
       
        $tableauEmploye = array();

        foreach($employe as $key => $value) {

            $tableauEmploye[$key] = array(
                'id'=>$value->getId(),
                'numero' => $value->getNumero(),
                'username' => $value->getUsername(),
            );
        }

        return new JsonResponse($tableauEmploye);
    }

    #[Route('/liste_employe', name: 'app.liste.employe')]
    public function listeEmploye(Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{       
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();

                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }
        }
        $employe = $this->employeRepo->findAll();
        return $this->render('liste_employe.html.twig', [
            'controller_name' => 'EmployeController',
            'employe'=>$employe,
        ]);
    }

    #[Route('employe_show-{id}', name: 'app.employe.show')]
    public function show(Request $request,Security $security, Employe $employe,int $id)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{       
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();

                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }
        }
        $employe=$this->gerantRepo->listeGerant($id);
        dd($employe);
        return $this->render('compte_employe.html.twig', [
            'employe'=>$employe,
            
        ]);
    }

    #[Route('factureShowEmploye-{id}', name: 'app.factureShowEmploye')]
    public function factureShowEmploye(Request $request,Security $security, Facture $facture,int $id)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{       
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();

                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }
        }
        $employe=$this->gerantRepo->listeGerant($id);
        dd($employe);
        return $this->render('compte_employe.html.twig', [
            'employe'=>$employe,
            
        ]);
    }
    #[Route('suspendre_employe-{id}', name: 'app.employe.suspendre')]
    public function suspendre_employe(Request $request,Security $security, Employe $employe, int $id): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
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

        if($employe->isStatut()==0){
            $employe->setStatut(true);
        }
        else if($employe->isStatut()==1){
            $employe->setStatut(false);
        }
        $this->em->persist($employe);
        $this->em->flush();
        return $this->redirectToRoute('app.liste.employe');
    }
}
