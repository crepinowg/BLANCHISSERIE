<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//use Twilio\Rest\Clients;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\AdministrateurRepository;
use App\Entity\Administrateur;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeZone;
use Monolog\DateTimeImmutable;
use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Entity\Gerant;
use App\Entity\CodeUiAll;
use App\Service\SmsService;
use App\Entity\Employe;
use App\Entity\Notifications;
use App\Repository\EmployeRepository;
use App\Repository\EmployeEquipeRepository;
use App\Repository\CodeUiAllRepository;
use App\Repository\EquipeRepository;
use App\Repository\ClientRepository;
use App\Repository\GerantRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\Autowire\Autowire;
use App\Entity\Equipe;
use App\Entity\EmployeEquipe;
use App\Entity\Entreprise;
use App\Service\MailerService;
use App\Controller\FunctionImplementController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterUpdateController extends AbstractController
{

      /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;


      public function __construct(
        AdministrateurRepository $repoAdmin,
        MailerService $mailerService,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EmployeEquipeRepository $repoEmployeEquipe, 
        EmployeRepository $repoEmploye,
        UtilisateurRepository $utilisateurRepo,
        EquipeRepository $repoEquipe,
        FunctionImplementController $functionImplement,
        ClientRepository $repoClient,
        GerantRepository $repoGerant,
        CodeUiAllRepository $repoCodeUiAll, 
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->repoAdmin= $repoAdmin;
        $this->mailerService= $mailerService;
        $this->tokenGeneratorInterface= $tokenGeneratorInterface;
        $this->repoEmployeEquipe= $repoEmployeEquipe;
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


    #[Route('/client_update-{id}', name: 'app.client.update')]
    public function client_update(Request $request , Security $security,UserPasswordHasherInterface $passwordHasher, Client $client, int $id): Response
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
        $utilisateur = $this->utilisateurRepo->findClientById($id);
        

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
            
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

           
            $username=strtolower($request->request->get('username'));
            $email=strtolower($request->request->get('email'));
            $numero=$request->request->get('contact');
            $adresse=$request->request->get('adresse');
            $zip=$request->request->get('zip');
            $sexe=$request->request->get('sexe');
            $indication=$request->request->get('indication');
            $gps=($request->request->get('gps'));
            $password="1234";
            $numeroCheck=false;
            $emailCheck=false;
            $statut="N/A";
            $nom=strtoupper($request->request->get('nom'));
            $createdAt=Date('Y-m-d h:i');
            if ($request->isXmlHttpRequest()) {
                
                if ($nom==null OR $username==null OR $email==null OR $numero==null OR $adresse==null OR $zip==null OR $indication==null){
                    
                    $message="Veuillez remplir tout les champs requis avant soumission";            
                    return $this->json($message);

                }

                if (strlen($numero)>8){
                    $message="Numéro de téléphone trop long!";            
                    return $this->json($message);     
                }
                
                
                $clients = $this->utilisateurRepo->findDataClient();
                foreach ($clients as $key => $value) {
                    if ($value->getUsername()== $username AND $value->getClient()->getId()!=$id){
                        $message="Le username existe déjà!";            
                        return $this->json($message);     
                    }
                    if ($value->getEmail()== $email AND $value->getClient()->getId()!=$id){
                        $message="Cette adresse Email existe déjà!";            
                        return $this->json($message);     
                    }

                    if ($value->getNumero()== $numero AND  $value->getClient()->getId()!=$id){
                        $message="Numéro de téléphone existant!";            
                        return $this->json($message);     
                    }
                }

                if(strpos($numero, "-") !== false){
                    $message="Aucun caractère aurorisé dans le champ 'Numéro' !";
                    
                    return $this->json($message);
                }

                
                
                //dd($nom."-".$username."-".$email."-".$numero."-".$adresse."-".$zip."-".$indication);
                if (isset($nom,$username,$email,$numero,$adresse,$zip,$indication)){
                    foreach ($utilisateur as $key => $value) {
                         //dd("OAKY");
                        $value->setNom($nom);
                        $value->setUsername($username);
                        
                        $value->setNumero($numero);
                        $value->setAdresse($adresse);
                        $value->setSexe($sexe);
                        $value->setCreatedAt($createdAt);

                        //return $this->json($value->getEmail());
                        if($value->getEmail() != $email){
                            $value->setEmail($email);
                            $value->setIsVerify(0);
                            $now = new \DateTime('now');
                            $nextDay = ($now->add(new \DateInterval('P1D')))->format('Y-m-d h:i');
                            $tokenRegistration = $this->tokenGeneratorInterface->generateToken();
                            $value->setTokenRegistration($tokenRegistration);
                            $value->setTokenRegistrationLifeTime($nextDay);
                            
                            $this->mailerService->sendEmailFacture(
                                $email,
                                "Confirmation d'email",
                                "mail_registration_verify.html.twig",
                                [
                                    "user"=>$value,
                                    "username"=>$username,
                                    "tokenRegistration" => $tokenRegistration,
                                    "tokenLifeTime" => $value->getTokenRegistrationLifeTime(),
                                ]
                                
                            );
                            
                            
                        }
            
                        
                    
                   
                    //dd($admin);
                    

                    /*if($checkUser == false){
                        $value->setCreatedByGerant($gerantUser);
                        $client->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                       // $client->setAdministrateur();
                        $value->setCreatedByAdmin($admin);
                        
                    }*/
                }


                    $client->setStatut($statut);
                    $client->setZip($zip);
                    if ($gps != null) {
                        $client->setGpsLink($gps);
                    }
                    $client->setIndication($indication);
                    $this->em->persist($client);
                    foreach ($utilisateur as $key => $value) {
                        $value->setClient($client);
                        $this->em->persist($value);
                    }
                    $notifications->setTitre("Update Client");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $notifications->setClient($client);
                    $this->em->persist($notifications);

                    
                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.client.liste'),
                    ]);
                    
                }
               
            }

            else{
                $client = $this->repoClient->find($id);
                return $this->render('update_client.html.twig', [
                'controller_name' => 'ClientController',
                "client"=>$client
            ]);
            }

        return $this->redirectToRoute('app.equipe.liste');

    }

    #[Route('/gerant_update-{id}', name: 'app.gerant.update')]
    public function gerant_update(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher,Gerant $gerant, int $id): Response
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR') OR $this->authorizationChecker->isGranted('ROLE_ADMIN'))){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();

                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

                if ($gerant->isStatut() == 1 OR ($this->getUser()->getGerant() != $gerant AND !$this->authorizationChecker->isGranted('ROLE_ADMIN'))){
                    return $this->redirectToRoute('app.notfound');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }
        
        $utilisateur = $this->utilisateurRepo->findGerantById($id);
        foreach ($utilisateur as $key => $value) {
            $idUsers = $value->getId();
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
            
            $nom=strtoupper($request->request->get('nom'));
            $username=strtolower($request->request->get('username'));
            $email=strtolower($request->request->get('emailPerso'));
            $numero=$request->request->get('contPerso');
            $adresse=$request->request->get('adresse');
            $password=$request->request->get('password');
            $salaire=$request->request->get('salaire');
            $niveau=$request->request->get('niveau');

            $numeroCheck=false;
            $emailCheck=false;
            $createdAt=Date('Y-m-d h:i');

            //dd("Nom:".$nom." Username:".$username." Mail:".$email." Numéro:".$numero." Password:".$password." Adresse:".$adresse." Niveau:".$niveau);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {

                if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            
                    $checkUser=true;
                    $admin = $this->getUser()->getAdministrateur();
                    
                    if ($salaire == null){
                        $message="Veuillez renseigner un salaire";            
                        return $this->json($message);
                    }
                    else{
                        $gerant->setSalaire($salaire);
                        $this->em->flush();
                        return new JsonResponse([
                            'success' => true,
                            'redirect_url' => $this->generateUrl('app.liste.gerant'),
                        ]);
                    }
                    
                }
                
                if ($nom==null OR $username==null OR $email==null OR $numero==null OR $adresse==null){
                    
                    $message="Veuillez remplir tout les champs requis avant soumission";            
                    return $this->json($message);

                }
                if (strlen($numero)>8){
                    $message="Numéro de téléphone trop long!";            
                    return $this->json($message);     
                }

                if ($password != null AND strlen($password)<8){
                    $message="Mot de passe faible. Saisir minimum  8 caractères, majuscules, miniscules, chiffres...";            
                    return $this->json($message);     
                }


                $gerants = $this->utilisateurRepo->findDataGerant();

                foreach ($gerants as $key => $value) {
                    if ($value->getGerant()== null){
                        if ($value->getUsername()== $username){
                            $message="Le username existe déjà!";            
                            return $this->json($message);     
                        }
                        if ($value->getEmail()== $email ){
                            $message="Cette adresse Email existe déjà!";            
                            return $this->json($message);     
                        }
    
                        if ($value->getNumero()== $numero ){
                            $message="Numéro de téléphone existant!";            
                            return $this->json($message);     
                        }
                    }

                    if ($value->getGerant()!= null){
                        if ($value->getUsername()== $username AND $value->getGerant()->getId()!= $id){
                            $message="Le username existe déjà!";            
                            return $this->json($message);     
                        }
                        if ($value->getEmail()== $email AND $value->getGerant()->getId()!= $id){
                            $message="Cette adresse Email existe déjà!";            
                            return $this->json($message);     
                        }

                        
    
                        if ($value->getNumero()== $numero AND $value->getGerant()->getId()!= $id){
                            $message="Numéro de téléphone existant!";            
                            return $this->json($message);     
                        }
                    }
                    
                    
                }
                
                if (isset($nom,$username,$email,$numero,$adresse)){
                 

                    foreach ($utilisateur as $key => $value) {
                        if ($value->getEmail() != $email){
                            $now = new \DateTime('now');
                            $nextDay = ($now->add(new \DateInterval('P1D')))->format('Y-m-d h:i');
                            $tokenRegistration = $this->tokenGeneratorInterface->generateToken();
                            $value->setTokenRegistration($tokenRegistration);
                            $value->setTokenRegistrationLifeTime($nextDay);

                            $value->setNom($nom);
                            $value->setUsername($username);
                            $value->setEmail($email);
                            $value->setNumero($numero);
                            $value->setIsVerify(0);
                            if($password!=null){
                                $hashedPassword = $passwordHasher->hashPassword($value,$password);
                                $value->setMotDePasse($hashedPassword);
                            }
                            
                            
                            $value->setAdresse($adresse);
                            if($checkUser == false){
                                $value->setCreatedByGerant($gerantUser);
                            }

                            else if($checkUser == true){
                                $value->setCreatedByAdmin($admin);
                            }
                            $this->em->persist($gerant);
                            $value->setGerant($gerant);
                            $this->em->persist($value);
                            
                            $this->mailerService->sendEmailFacture(
                                $email,
                                "Confirmation d'email",
                                "mail_registration_verify.html.twig",
                                [
                                    "user"=>$value,
                                    "username"=>$username,
                                    "tokenRegistration" => $tokenRegistration,
                                    "tokenLifeTime" => $value->getTokenRegistrationLifeTime(),
                                ]                    
                            );

                            $this->em->flush();
                            
                            return new JsonResponse([
                                'success' => true,
                                'redirect_url' => $this->generateUrl('app.logout'),
                            ]);  
                        

                        
                        }
                        
                        $value->setNom($nom);
                        $value->setUsername($username);
                        $value->setEmail($email);
                        $value->setNumero($numero);
                        if($password!=null){
                            $hashedPassword = $passwordHasher->hashPassword($value,$password);
                            $value->setMotDePasse($hashedPassword);
                        }
                        
                          
                        $value->setAdresse($adresse);
                        if($checkUser == false){
                            $value->setCreatedByGerant($gerantUser);
                        }

                        else if($checkUser == true){
                            $value->setCreatedByAdmin($admin);
                        }

                    
                        
                        $this->em->persist($gerant);
                        $value->setGerant($gerant);
                        $this->em->persist($value);
                        
                        
                        $this->em->flush();

                        
                    }
                    
                    

                    

                    
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.account'),
                    ]);
                    
                }
            }
            else{
                $gerant = $this->repoGerant->find($id);
                return $this->render('update_gerant.html.twig',[
                    "gerant"=>$gerant,
                ]);
            }
       

        return $this->redirectToRoute('app.liste.gerant');
    }

    #[Route('/employe_update-{id}', name: 'app.employe.update')]
    public function employe_register(Request $request , Security $security,Employe $employe, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if(($this->authorizationChecker->isGranted('ROLE_LAVAGE') OR $this->authorizationChecker->isGranted('ROLE_LIVREUR') OR $this->authorizationChecker->isGranted('ROLE_ADMIN'))){
               
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
        $utilisateur = $this->utilisateurRepo->findEmployeById($id);
        $notifications = new Notifications;
        
        
            $nom=strtoupper($request->request->get('nom'));
            $username=strtolower($request->request->get('username'));
            $numero=$request->request->get('contPerso');
            $adresse=$request->request->get('adresse');
            $salaire=$request->request->get('salaire');
            $password=$request->request->get('password');
            $type=$request->request->get('employe');

            $numeroCheck=false;
            $emailCheck=false;
            $createdAt=Date('Y-m-d h:i');

            //dd("Nom:".$nom." Username:".$username." Mail:".$email." Numéro:".$numero." Password:".$password." Adresse:".$adresse." Payement:".$payement." Employe:".$employe." Salaire:".$salaire);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {

                if(($this->authorizationChecker->isGranted('ROLE_ADMIN'))){
                    if ($salaire == null or $type==null){
                        $message="Veuillez renseigner un salaire et le type d'employé";            
                        return $this->json($message);
                    }
                    else{
                        $employe->setSalaire($salaire);
                        if($type == 3){
                            $employe->setRoles([
                                "ROLE_LIVREUR"
                            ]);
                        }
                        elseif($type == 2){
                            $employe->setRoles([
                                "ROLE_LAVAGE"
                            ]);
                        }
                        $this->em->flush();
                        return new JsonResponse([
                            'success' => true,
                            'redirect_url' => $this->generateUrl('app.account'),
                        ]);
                    }
                    
                    
                }
                elseif(($this->authorizationChecker->isGranted('ROLE_LAVAGE') OR $this->authorizationChecker->isGranted('ROLE_LIVREUR'))){

                    if ($nom==null OR $username==null OR $numero==null OR $adresse==null){
                        
                        $message="Veuillez remplir tout les champs requis avant soumission";            
                        return $this->json($message);

                    }
                    if (strlen($numero)>8){
                        $message="Numéro de téléphone trop long!";            
                        return $this->json($message);     
                    }

                    if ($password!= null AND strlen($password)<8){
                        $message="Mot de passe: minimum 8 caratères";            
                        return $this->json($message);     
                    }

                    

                    
                    
                    $clients = $this->utilisateurRepo->findDataClient();
                    foreach ($clients as $key => $value) {
                        if ($value->getUsername()== $username AND $value->getEmploye()->getId()!= $id){
                            $message=$value->getEmploye()->getId();            
                            return $this->json($message);     
                        }
                        if ($value->getNumero()== $numero AND $value->getEmploye()->getId()!= $id){
                            $message="Numéro de téléphone existant!";            
                            return $this->json($message);     
                        }
                    }
                    if (isset($nom,$username,$numero,$adresse)){
                        foreach ($utilisateur as $key => $value) {
                            $value->setNom($nom);
                            $value->setUsername($username);
                            if($password!=null){
                                $hashedPassword = $passwordHasher->hashPassword($value,$password);
                                $value->setMotDePasse($hashedPassword);
                            }
                            //$utilisateur->setEmail($email);
                            $value->setNumero($numero);
                            $value->setAdresse($adresse);

                            $this->em->persist($value);
                        }


                        $this->em->persist($employe);
                        foreach ($utilisateur as $key => $value) {
                            $value->setEmploye($employe);
                            $this->em->persist($value);
                        }
                        

                        $notifications->setTitre("Update Employe");
                        $notifications->setReader(false);
                        $notifications->setCreatedAt(date("Y-m-d h:i"));
                        $notifications->setTypeNotif("MJ");
                    

                        $notifications->setEmploye($employe);
                        $this->em->persist($notifications);
                        
                        $this->em->flush();
                        return new JsonResponse([
                            'success' => true,
                            'redirect_url' => $this->generateUrl('app.account'),
                        ]);
                        
                    }
                }
            }
            else{
                $employe = $this->repoEmploye->find($id);
                return $this->render('update_employe.html.twig', [
                    'controller_name' => 'ClientController',
                    "employe"=>$employe
                ]);
            }
               
    }

    #[Route('update_equipe-{id}', name: 'app.equipe.update')]
    public function update_equipe(Equipe $equipe, Security $security,int $id, Request $request): Response
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

        $notifications = new Notifications;
        
        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

            $nom=strtoupper($request->request->get('nom'));
            $description=$request->request->get('description');
            $createdAt=Date('Y-m-d h:i');
       
            //dd("Nom:".$nom." Username:".$username." Mail:".$email." Numéro:".$numero." Password:".$password." Adresse:".$adresse." Payement:".$payement." Employe:".$employe." Salaire:".$salaire);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {
                if ($nom==null){
                    
                    $message="Le nom de l'équipe est requis";            
                    return $this->json($message);

                }
                
                
                $equipes = $this->repoEquipe->findDataEquipe();
                foreach ($equipes as $key => $value) {
                    if ($value->getNom()== $nom AND $value->getId()!=$id){
                        $message="Le nom de l'équipe existe déjà";            
                        return $this->json($message);     
                    }
                    
                }
                if (isset($nom)){
                    $equipe->setNom($nom);
                    $equipe->setDescription($description);
                    $equipe->setCreatedAt($createdAt);

                    if($checkUser == false){
                        $equipe->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $equipe->setAdministrateur($admin);
                    }

                

                    $this->em->persist($equipe);

                    $nombreEmploye = $this->repoEmploye->countEmploye();
                    $allEmploye = $this->repoEmploye->findAll();
                    $allEquipeEmploye = $this->repoEmployeEquipe->employeViaEmployeEquipe($id);

                    foreach ($allEquipeEmploye as $key => $value) {
                        $this->em->remove($value);
                    }

                    $iterationEmploye = 0;
                    foreach ($allEmploye as $key => $value) {
                       

                        $employe_equipe = "employe_equipe".$key+1;
                        
                        $employe_equipe = new EmployeEquipe;
                        

                        $employe = "employe".$key+1;
                        $employe= $request->request->get('employe'.$key+1);
                        $checkEmploye = false;
                       
                    
                        if($employe == true){
                            $iterationEmploye = $iterationEmploye +$key+1;
                            $employe_equipe->setEmploye($value);
                            $employe_equipe->setEquipe($equipe);
                            $this->em->persist($employe_equipe);
                            
                        }
                        if($iterationEmploye <2 && ($key+1)==count($allEmploye)){

                            $message="Choisissez minimum 2 employé. Un responsable lavage et un repasseur serait recommendés";            
                            return $this->json($message);
                        }
                       /* if($employe == null && ($key+1)==count($allEmploye)){

                            $message="Vous devez choisir un employé";            
                            return $this->json($message);
                        }*/
                     
                    }
                   
                    $notifications->setTitre("Update Equipe");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");

                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }

                    $notifications->setEquipe($equipe);

                    
                    $this->em->persist($notifications);
                    
                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.equipe.liste'),
                    ]);

                    
                }
            }
            else{
                $employe = $this->repoEmploye->findAll();
                $equipe = $this->repoEquipe->find($id);
            return $this->render('update_equipe.html.twig', [
                'employe'=>$employe,
                'equipe'=>$equipe
            ]);
            }
        
    }

    #[Route('/register_update-{id}', name: 'app.register.update')]
    public function register_update(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher, Administrateur $admin, int $id)
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

        $utilisateur = $this->getUser();

        $nom=$request->request->get('nom');
        $username=strtolower($request->request->get('username'));
        $emailPerso=$request->request->get('emailPerso');
        $contactPerso=$request->request->get('contPerso');
        $adresse=$request->request->get('adresse');
        $createdAt=Date('Y-m-d h:i');
        $password=$request->request->get('password');
        //$photoProfileFile = $request->request->get('photoProfile')->getData();

        $nomEntreprise=$request->request->get('nomEntreprise');
        $adresseEntreprise=$request->request->get('adresseEntreprise');
        $numeroPersoCheck=false;
        $emailPersoCheck=false;
        $numeroEntrepriseCheck=false;
        $emailEntrepriseCheck=false;
        $emailEntre=$request->request->get('emailEntre');
        $contactEntre=$request->request->get('contEntre');
        if ($request->isXmlHttpRequest()) {
           
            if ($nom==null OR $username==null OR $emailPerso==null OR $contactPerso==null OR $adresse==null  OR $nomEntreprise==null OR $adresseEntreprise==null OR $emailEntre==null OR $contactEntre==null){
                    
                $message="Veuillez remplir tout les champs requis avant soumission";            
                return $this->json($message);

            }

            if (strlen($contactPerso)>8 OR strlen($contactEntre)>8){
                $message="Numéro de téléphone trop long!";            
                return $this->json($message);     
            }

            if ($password!= null AND strlen($password)<8){
                $message="Mot de passe faible. Saisir minimum  8 caractères, majuscules, miniscules, chiffres...";            
                return $this->json($message);     
            }
            
            $userAdmin = $this->utilisateurRepo->findDataGerant();
            foreach ($userAdmin as $key => $value) {
                if ($value->getUsername()== $username AND $value->getAdministrateur()!= $admin){
                    $message="Le username existe déjà!";            
                    return $this->json($message);     
                }
                if ($value->getEmail()== $emailPerso AND $value->getAdministrateur()!= $admin){
                    $message="Cette adresse Email Perso existe déjà!";            
                    return $this->json($message);     
                }
                if ($value->getEmail()== $emailEntre AND $value->getAdministrateur()!= $admin){
                    $message="Cette adresse Email Pro existe déjà!";            
                    return $this->json($message);     
                }

                if ($value->getNumero()== $contactPerso AND $value->getAdministrateur()!= $admin){
                    $message="Numéro personnel de téléphone existant!";            
                    return $this->json($message);     
                }
                if ($value->getNumero()== $contactEntre AND $value->getAdministrateur()!= $admin){
                    $message="Numéro d'entreprise de téléphone existant!";            
                    return $this->json($message);     
                }
            }
            
            
            $utilisateur->setNom($nom);
            $utilisateur->setUsername($username);
            if($password!=null){
                $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
                $utilisateur->setMotDePasse($hashedPassword);
            }
            if ($utilisateur->getEmail()!=$emailPerso) {
                $utilisateur->setEmail($emailPerso);
                $utilisateur->setEmailCheck(false);
            }
            else if ($utilisateur->getEmail()==$emailPerso) {
                $utilisateur->setEmail($emailPerso);
            }

            if ($utilisateur->getNumero()!=$contactPerso) {
                $utilisateur->setNumero($contactPerso);
                $utilisateur->setNumeroCheck($numeroPersoCheck);
            }
            else if ($utilisateur->getEmail()==$contactPerso) {
                $utilisateur->setNumero($contactPerso);
            }

            $utilisateur->setAdresse($adresse);

            foreach ($admin->getEntreprise() as $key => $entreprise) {
                if ($entreprise->getEmailEntre()!=$emailEntre) {
                    $entreprise->setEmailEntre($emailEntre);
                    $entreprise->setEmailEntrepriseCheck($emailEntrepriseCheck);
                }
                else if ($entreprise->getEmailEntre()==$emailEntre) {
                    $entreprise->setEmailEntre($emailEntre);
                }
    
                if ($entreprise->getNumeroTelEntre()!=$contactEntre) {
                    $entreprise->setNumeroTelEntre($contactEntre);
                    $entreprise->setNumeroEntrepriseCheck($numeroEntrepriseCheck);
                }
                else if ($entreprise->getNumeroTelEntre()==$contactEntre) {
                    $entreprise->setNumeroTelEntre($contactEntre);
                }
                
                $entreprise->setNom($nomEntreprise);
                $entreprise->setAdresse($adresseEntreprise);
                $this->em->persist($entreprise);
            }
            
            $this->em->persist($admin);
            $this->em->persist($utilisateur);
            
            $this->em->flush();
            /*$numeroEnv = "96761412";
            $sender = "+22896761412";
            $message = $this->twilio->messages->create(
                $numeroEnv, // Envoyer un message à ce numéro
                array(
                'from' => $sender, // numéro de téléphone Twilio
                'body' => 'Bonjour! Nous vous rappelons que votre rendez-vous est prévu pour aujourd\'hui à '
                )
            );*/
            
            return new JsonResponse([
                'success' => true,
                'redirect_url' => $this->generateUrl('app.account'),
            ]);

            
        }
        else{
            return $this->render('account.html.twig',[
                'employe'=>"employe",
            ]);
        }
    }



}
