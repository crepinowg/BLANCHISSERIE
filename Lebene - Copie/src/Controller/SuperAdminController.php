<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\AdministrateurRepository;
use App\Entity\Administrateur;
use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Entity\Gerant;
use App\Controller\FunctionImplementController;
use App\Entity\CodeUiAll;
use App\Entity\Employe;
use App\Entity\SuperAdmin;
use App\Entity\CleProduit;
use App\Entity\Notifications;
use App\Repository\EmployeRepository;
use App\Repository\CodeUiAllRepository;
use App\Repository\EquipeRepository;
use App\Repository\ClientRepository;
use App\Repository\GerantRepository;
use App\Repository\CleProduitRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\Autowire\Autowire;
use App\Entity\Equipe;
use App\Entity\EmployeEquipe;
use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime; 

class SuperAdminController extends AbstractController
{

     /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;



      public function __construct(
        AdministrateurRepository $repoAdmin,
        CleProduitRepository $repoCle, 
        EmployeRepository $repoEmploye,
        FunctionImplementController $functionImplement,
        UtilisateurRepository $utilisateurRepo,
        EquipeRepository $repoEquipe,
        ClientRepository $repoClient,
        GerantRepository $repoGerant,
        CodeUiAllRepository $repoCodeUiAll, 
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->repoAdmin= $repoAdmin;
        $this->repoCle= $repoCle;
        $this->repoEmploye= $repoEmploye;
        $this->functionImplement = $functionImplement;
        $this->repoEquipe= $repoEquipe;
        $this->repoGerant= $repoGerant;
        $this->repoClient= $repoClient;
        $this->utilisateurRepo= $utilisateurRepo;
        $this->repoCodeUiAll= $repoCodeUiAll;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('/sa', name: 'app.super.admin')]
    public function index(Security $security,): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
               

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }
        
        return $this->render('sa_register.html.twig', [
            'controller_name' => 'SuperAdminController',
        ]);
    }

    #[Route('/saisir_cle-{id}', name: 'app.saisir.cle')]
    public function saisir_cle(Administrateur $admin, Security $security,int $id, Request $request): Response
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                
                if ($suspendu == 0){
                    return $this->redirectToRoute('app.accueil');
                }
            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $allCle = $this->repoCle->findByAdmin($id);
        $touteCle = $this->repoCle->findAll();
        $cle=$request->request->get('cle');
        $check = false; 
        $check2 = false; 
        if(isset($cle)){
            foreach ($touteCle as $key => $value) {
                if ($value->isStatut()==1 AND $value->isEtat()==0) {
                    $check = true; 
                    
                }
            }
            foreach ($touteCle as $key => $value) {
                if ($value->getCle()==$cle AND $value->isStatut()==0 AND $value->isEtat()==0 AND $check == false) {
                    //dd($check);
                    $check2 = true;
                    $admin->setSuspendu(false);
                    $value->setStatut(true);
                    $dateToday = new DateTime();
                    $useAt = Date('Y-m-d h:i');
                    $value->setUseAt($useAt);
    
                    $date = new DateTime($value->getUseAt());
                    $diff = $date->diff($dateToday);
                    $diffEnJours = $diff->days;
                    $dureeFinal = $value->getDuree() - $diffEnJours;
                    $value->setDureeReste($dureeFinal);
                    $this->em->persist($value);
                    if($dureeFinal==0){
                        $value->setEtat(true);
                        $this->em->persist($value);
                    }
                    $this->em->flush();
                    return $this->redirectToRoute('app.cle.liste');
                          
                }

                if($check2 == false AND $key == sizeof($touteCle)-1){
                    //dd($value->getCle().' - '.$cle);
                    return $this->render('saisir_cle.html.twig', [
                        'controller_name' => 'SuperAdminController',
                        "allCle"=>$allCle,
                        "id"=>$id
                    ]);
                }
              
            }
            //$this->em->flush();
            //return $this->redirectToRoute('app.cle.liste');
           
        }
        else{
            return $this->render('saisir_cle.html.twig', [
                'controller_name' => 'SuperAdminController',
                "allCle"=>$allCle,
                "id"=>$id
            ]);
        }
        
    }

    #[Route('/liste_cle', name: 'app.cle.liste')]
    public function liste_cle(Security $security,): Response
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
               

            }
            else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
               
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
        $allCle = $this->repoCle->findAll();

        foreach ($allCle as $key => $value) {

            
            if($value->isStatut()==1 AND $value->getDureeReste()>0){
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
                }
            }
           
        }
        $this->em->flush();
      

        
        return $this->render('info_cle.html.twig', [
            'controller_name' => 'SuperAdminController',
            "allCle"=>$allCle
        ]);
    }

    #[Route('/generer_cle', name: 'app.generer.cle')]
    public function generer_cle(Request $request,Security $security,): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
             

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }


        $allAdmin = $this->repoAdmin->findAll();
        $adminId=$request->request->get('adminId');
        $duree=$request->request->get('duree');
        $prix=$request->request->get('prix');
        $statut=false;
        $createdAt=Date('Y-m-d h:i');
        $cle = new CleProduit;
        
        if (isset($duree,$adminId,$prix)){
            //dd($adminId,$duree);
            $cle->setDuree($duree);
            $cle->setCreatedAt($createdAt);
            $cle->setEtat(false);
            $cle->setPrix($prix);


            $date = new DateTime();
            $nouvelleDate = clone $date;
            $nouvelleDate->sub(new \DateInterval('P'.$duree.'D'));
            $diffEnJours = $nouvelleDate->diff($date)->days;

            $cle->setDureeReste($diffEnJours);

            $allCle = $this->repoCle->findAll();

            if($allCle==null){
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $random_code = substr(str_shuffle($chars), 0, 16);
                $cle->setCle($random_code);
                $cle->setStatut($statut);                
            }
            
            foreach ($allCle as $key => $value) {
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $random_code = substr(str_shuffle($chars), 0, 16);
                if ($value->getCle()==$random_code){
                    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                    $random_code = substr(str_shuffle($chars), 0, 16);
                    $cle->setCle($random_code);
                    $cle->setStatut($statut); 
                }
                if ($value->getCle()!=$random_code){
                    $cle->setCle($random_code);
                    $cle->setStatut($statut);    
                }

            }

            $admin= $this->repoAdmin->find($adminId);
            $cle->setAdministrateur($admin);   

            $this->em->persist($cle);
            $this->em->flush();
            return $this->redirectToRoute('app.cle.liste');
        }
        else{
            return $this->render('creation_cle.html.twig', [
                'controller_name' => 'SuperAdminController',
                "allAdmin"=>$allAdmin
            ]);
        }
       
    }

    #[Route('/saa', name: 'app.super.adminn')]
    public function saa(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher): Response
    {

        /*if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
              

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }*/

        $utilisateur = new Utilisateur;
        $superAdmin = new SuperAdmin;

        $nom=$request->request->get('nom');
        $username=$request->request->get('username');
        $contactPerso=$request->request->get('numero');
        $adresse=$request->request->get('adresse');
        $createdAt=Date('Y-m-d h:i');
        $password=$request->request->get('password');

        $numeroPersoCheck=false;
        $emailPersoCheck=false;

        if (isset($nom,$username,$contactPerso,$password,$adresse)){
           
           $utilisateur->setNom($nom);
           $utilisateur->setUsername($username);
           $utilisateur->setNumero($contactPerso);
           $utilisateur->setAdresse($adresse);
           $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
           $utilisateur->setMotDePasse($hashedPassword);
           $utilisateur->setCreatedAt($createdAt);
           $utilisateur->setEmailCheck($emailPersoCheck);
           $utilisateur->setNumeroCheck($numeroPersoCheck);
           $utilisateur->setRoles(["ROLE_SUPER_ADMIN"]);
            
           $this->em->persist($superAdmin);
           $utilisateur->setSuperAdmin($superAdmin);
           $this->em->persist($utilisateur);
           
           $this->em->flush();
          
          
        }
        else{
            return $this->render('sa_register.html.twig', [
                'controller_name' => 'SecurityController',
            ]);
        }
        return $this->redirectToRoute('app.security');
        
    }
}
