<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Depense;
use App\Entity\Notifications;
use Symfony\Component\Security\Core\Security;

use App\Controller\FunctionImplementController;
use App\Repository\DepenseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\Autowire\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;

class DepensesController extends AbstractController
{
     /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct( 
        DepenseRepository $depensesRepo, 
        EntityManagerInterface $em,
        FunctionImplementController $functionImplement,
        AuthorizationCheckerInterface $authorizationChecker,
        ){
        
        $this->em = $em;
        $this->depensesRepo = $depensesRepo;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('/liste_depenses', name: 'app.depenses.liste')]
    public function liste_depenses(Security $security): Response
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
        $depenses = $this->depensesRepo->findAll();
        //$this->addFlash('NoClient','Aucun client enregistrer. Ajouter  pour commencer');
        return $this->render('liste_depenses.html.twig', [
            'controller_name' => 'ClientController',
            'depenses'=>$depenses
        ]);
       
    }

    #[Route('/supprimer_depenses-{id}', name: 'app.depenses.supprimer')]
    public function supprimer_depenses(Depense $depense, int $id,Security $security): Response
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

        $depense->setDeleted(true);
        $this->em->persist($depense);
        $this->em->flush();
        return $this->redirectToRoute('app.depenses.liste');
       
    }

    #[Route('/depenses_ajout', name: 'app.depenses.ajout')]
    public function ajoutDepenses(Request $request,Security $security): Response
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
        $depenses = new Depense;
        $notifications = new Notifications;

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
            
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

        $nomProduit=$request->request->get('nomProduit');
        $description=$request->request->get('description');
        $prixTotal=$request->request->get('prixTotal');
        $typeDepense=$request->request->get('typeDepense');
        //dd($nomProduit);
        if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            if ($request->isXmlHttpRequest()) {

                if ($nomProduit == null && $description  == null && $prixTotal == null && $typeDepense == null){
                    
                    $message=$typeDepense;            
                    return $this->json($message);

                }
                if ($prixTotal<100){
                    
                    $message="Veuillez revoir le prix total: il doit être au moins égal à 100F";            
                    return $this->json($message);

                }
                if (strpos($prixTotal, "-") !== false){
                    
                    $message="Saisir un prix valide";            
                    return $this->json($message);

                }
                if (isset($nomProduit,$description,$prixTotal,$typeDepense)) {
                    $depenses->setNomProduit($nomProduit);
                    $depenses->setPrixTotal($prixTotal);
                    $depenses->setTypeDepense($typeDepense);
                    $depenses->setDescription($description);
                    $depenses->setCalculDepense(false);
                    $depenses->setCreatedAt(date("Y-m-d h:i"));
                    if($checkUser == false){
                        $depenses->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $depenses->setAdmin($admin);
                    }
                    $this->em->persist($depenses);

                    $notifications->setTitre("Mise à jour Dépense");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $notifications->setDepense($depenses);
                    $this->em->persist($notifications);

                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.depenses.liste'),
                    ]);
                }

                else{
                    return $this->render('ajout_depenses.html.twig', [
                    'controller_name' => 'DepensesController',
                    ]);
                }
            }
            else{
                return $this->render('ajout_depenses.html.twig', [
                    'controller_name' => 'ClientController',
                    'texte'=>"texte"
                ]);
            }
        }else{
            return $this->redirectToRoute('app.security');
        }
        return $this->redirectToRoute('app.depenses.liste');
    }


    #[Route('/depenses_update-{id}', name: 'app.depenses.update')]
    public function depenses_update(Request $request,Security $security, Depense $depenses, int $id): Response
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

        $notifications = new Notifications;

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
            
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

        $nomProduit=$request->request->get('nomProduit');
        $description=$request->request->get('description');
        $prixTotal=$request->request->get('prixTotal');
        $typeDepense=$request->request->get('typeDepense');
        //dd($nomProduit);
        if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            if ($request->isXmlHttpRequest()) {

                if ($nomProduit == null && $description  == null && $prixTotal == null && $typeDepense == null){
                    
                    $message=$typeDepense;            
                    return $this->json($message);

                }
                if ($prixTotal<100){
                    
                    $message="Veuillez revoir le prix total: il doit être au moins égal à 100F";            
                    return $this->json($message);

                }
                if (strpos($prixTotal, "-") !== false){
                    
                    $message="Saisir un prix valide";            
                    return $this->json($message);

                }
                if (isset($nomProduit,$description,$prixTotal,$typeDepense)) {
                    $depenses->setNomProduit($nomProduit);
                    $depenses->setPrixTotal($prixTotal);
                    $depenses->setTypeDepense($typeDepense);
                    $depenses->setDescription($description);
                    $depenses->setCalculDepense(false);
                    $depenses->setCreatedAt(date("Y-m-d h:i"));
                    if($checkUser == false){
                        $depenses->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $depenses->setAdmin($admin);
                    }
                    $this->em->persist($depenses);

                    $notifications->setTitre("Mise à jour Dépense");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $notifications->setDepense($depenses);
                    $this->em->persist($notifications);

                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.depenses.liste'),
                    ]);
                }

                else{
                    return $this->render('update_depenses.html.twig', [
                    'controller_name' => 'DepensesController',
                    'depenses'=>$depenses
                    ]);
                }
            }
            else{
                return $this->render('update_depenses.html.twig', [
                    'controller_name' => 'ClientController',
                    'depenses'=>$depenses
                ]);
            }
        }else{
            return $this->redirectToRoute('app.security');
        }
        return $this->redirectToRoute('app.depenses.liste');
    }

    
}
