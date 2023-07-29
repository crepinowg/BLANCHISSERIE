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
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeZone;
use Monolog\DateTimeImmutable;
use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Entity\Gerant;
use App\Entity\CodeUiAll;
use App\Controller\FunctionImplementController;
use App\Entity\Employe;
use App\Entity\Notifications;
use App\Repository\EmployeRepository;
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
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrerController extends AbstractController
{

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;



      public function __construct(
        AdministrateurRepository $repoAdmin, 
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

    #[Route('/register', name: 'app.register')]
    public function index(Request $request , Security $security,UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
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

        $admin = new Administrateur;
        $utilisateur = new Utilisateur;
        $entreprise = new Entreprise;

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
        

        if (isset($nom,$username,$emailPerso,$emailEntre,$contactPerso,$contactEntre,$password,$adresse,$adresseEntreprise,$nomEntreprise)){
           
           $utilisateur->setNom($nom);
           $utilisateur->setUsername($username);
           $utilisateur->setEmail($emailPerso);
           $utilisateur->setNumero($contactPerso);
           $utilisateur->setAdresse($adresse);
           $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
           $utilisateur->setMotDePasse($hashedPassword);
           $utilisateur->setCreatedAt($createdAt);
           $utilisateur->setEmailCheck($emailPersoCheck);
           $utilisateur->setNumeroCheck($numeroPersoCheck);
           $utilisateur->setRoles(["ROLE_ADMIN"]);
            /*dd($photoProfileFile);
            if ($photoProfileFile) {
                // Gérer le téléchargement et le stockage de la photo de profil
                $fileName = md5(uniqid()) . '.' . $photoProfileFile->guessExtension();
                dd($fileName);
                $photoProfileFile->move(
                    $this->getParameter('photo_profile_directory'),
                    $fileName
                );

                // Mettre à jour le chemin/nom du fichier de la photo de profil
                $user->setPhotoProfile($fileName);
            }*/

           $entreprise->setEmailEntre($emailEntre);
           $entreprise->setNumeroTelEntre($contactEntre);
           $entreprise->setEmailEntrepriseCheck($emailEntrepriseCheck);
           $entreprise->setNumeroEntrepriseCheck($numeroEntrepriseCheck);
           $entreprise->setNom($nomEntreprise);
           $entreprise->setAdresse($adresseEntreprise);
           
           $admin->setRoles(["ROLE_ADMIN"]);
           $admin->setSuspendu(true);
           $this->em->persist($admin);
           $entreprise->setAdministrateur($admin);
           $utilisateur->setAdministrateur($admin);
           $this->em->persist($utilisateur);
           $this->em->persist($entreprise);
           
           
           $this->em->flush();
           //dd($entreprise);
          
        }
        return $this->redirectToRoute('app.security');
    }


    #[Route('/gerant_register', name: 'app.gerant.register')]
    public function gerant_register(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher): Response
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

        $gerant = new Gerant;
        $utilisateur = new Utilisateur;
        $codeUiAll = new CodeUiAll;

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
            $email=strtolower($request->request->get('email'));
            $numero=$request->request->get('contact');
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
                if ($nom==null OR $username==null OR $password == null OR $email==null OR $numero==null OR $adresse==null){
                    
                    $message="Veuillez remplir tout les champs requis avant soumission";            
                    return $this->json($message);

                }

                if (strlen($numero)>8){
                    $message="Numéro de téléphone trop long!";            
                    return $this->json($message);     
                }

                if (strlen($password)<8){
                    $message="Mot de passe faible. Ajoutez y des caractères spéciaux. Minimum 8 caratères";            
                    return $this->json($message);     
                }

                if (strlen($salaire)>6 OR strlen($salaire)<5 OR ($salaire)<0){
                    $message="Le salaire est invalide. Le salaire doit être inférieur a 1 million";            
                    return $this->json($message);     
                }

            

                $gerants = $this->utilisateurRepo->findDataGerant();
                foreach ($gerants as $key => $value) {
                    if ($value->getUsername()== $username){
                        $message="Le username existe déjà!";            
                        return $this->json($message);     
                    }
                    if ($value->getEmail()== $email){
                        $message="Cette adresse Email existe déjà!";            
                        return $this->json($message);     
                    }

                    if ($value->getNumero()== $numero){
                        $message="Numéro de téléphone existant!";            
                        return $this->json($message);     
                    }
                }

                if (isset($nom,$username,$email,$numero,$password,$adresse,$salaire)){
                    $utilisateur->setNom($nom);
                    $utilisateur->setUsername($username);
                    $utilisateur->setEmail($email);
                    $utilisateur->setNumero($numero);
                    $utilisateur->setAdresse($adresse);
                    $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
                    $utilisateur->setMotDePasse($hashedPassword);
                    $utilisateur->setCreatedAt($createdAt);
                    if($checkUser == false){
                        $utilisateur->setCreatedByGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $utilisateur->setCreatedByAdmin($admin);
                    }

                    $utilisateur->setEmailCheck($emailCheck);
                    $utilisateur->setNumeroCheck($numeroCheck);

                   
                    $utilisateur->setRoles(["ROLE_GERANT_NOIR"]);
                    $gerant->setRoles(["ROLE_GERANT_NOIR"]);
                    

                    $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
                    $utilisateur->setMotDePasse($hashedPassword);

                    $codeUiFind = $this->repoCodeUiAll->findAll();

                    if($codeUiFind==null){
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        $gerant->setCodeUi($random_code);
                        $codeUiAll->setCodeUi($random_code);
                        
                    }
                    
                    foreach ($codeUiFind as $key => $value) {
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        if ($value->getCodeUi()==$random_code){
                            //pass
                        }
                        else if ($value->getCodeUi()!=$random_code){
                            $gerant->setCodeUi($random_code);
                            $codeUiAll->setCodeUi($random_code);
                        }

                    }
                    $gerant->setSalaire($salaire);
                    $this->em->persist($gerant);
                    $this->em->persist($codeUiAll);
                    $utilisateur->setGerant($gerant);
                    $this->em->persist($utilisateur);
                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.liste.gerant'),
                    ]);
                    
                }
                else{
                    return $this->render('register-gerant.html.twig');
                }
            }
            else{
                return $this->render('register-gerant.html.twig');
            }
        

        //return $this->redirectToRoute('app.liste.gerant');
    }

    #[Route('/registerEmploye', name: 'app.registerEmploye')]
    public function registerEmploye(Security $security,): Response
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
        return $this->render('register_employe.html.twig', [
            'controller_name' => 'SecurityController',
        ]);

    }

     #[Route('/register1', name: 'app.register1')]
    public function index1(Security $security,): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
               
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
        return $this->render('pages-register.html.twig', [
            'controller_name' => 'SecurityController',
        ]);

    }

     #[Route('/client_register', name: 'app.client.register')]
    public function client_register(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher): Response
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
        $client = new Client;
        $utilisateur = new Utilisateur;
        $notifications = new Notifications;
        $codeUiAll = new CodeUiAll;

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
                    if ($value->getUsername()== $username){
                        $message="Le username existe déjà!";            
                        return $this->json($message);     
                    }
                    if ($value->getEmail()== $email){
                        $message="Cette adresse Email existe déjà!";            
                        return $this->json($message);     
                    }

                    if ($value->getNumero()== $numero){
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
                    //dd("OAKY");
                    $utilisateur->setNom($nom);
                    $utilisateur->setUsername($username);
                    $utilisateur->setEmail($email);
                    $utilisateur->setNumero($numero);
                    $utilisateur->setAdresse($adresse);
                    $utilisateur->setSexe($sexe);
                    $utilisateur->setCreatedAt($createdAt);
                    //dd($admin);
                    

                    if($checkUser == false){
                        $utilisateur->setCreatedByGerant($gerantUser);
                        $client->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $client->setAdministrateur();
                        $utilisateur->setCreatedByAdmin($admin);
                        
                    }

                    $utilisateur->setEmailCheck($emailCheck);
                    $utilisateur->setNumeroCheck($numeroCheck);
                    $utilisateur->setRoles(["ROLE_CLIENT"]);
                    $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
                    $utilisateur->setMotDePasse($hashedPassword);

                    $codeUiFind = $this->repoCodeUiAll->findAll();
                    if($codeUiFind==null){

                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        $client->setCodeUi($random_code);
                        $codeUiAll->setCodeUi($random_code);
                        
                    }
                    foreach ($codeUiFind as $key => $value) {

                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        if ($value->getCodeUi()==$random_code){
                            //pass
                        }
                        else if ($value->getCodeUi()!=$random_code){
                            $client->setCodeUi($random_code);
                            $codeUiAll->setCodeUi($random_code);
                        }

                    }

                    $client->setRoles(["ROLE_CLIENT"]);
                    $client->setStatut($statut);
                    $client->setZip($zip);
                    $client->setIndication($indication);
                    $this->em->persist($client);
                    $this->em->persist($codeUiAll);
                    $utilisateur->setClient($client);
                    $this->em->persist($utilisateur);
                    $notifications->setTitre("Mise à jour Client");
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
                $client = $this->repoClient->findAll();
                return $this->render('forms-wizard.html.twig', [
                'controller_name' => 'ClientController',
            ]);
            }

        return $this->redirectToRoute('app.equipe.liste');

    }

    #[Route('/equipe_register', name: 'app.equipe.register')]
    public function equipe_register(Request $request , Security $security,UserPasswordHasherInterface $passwordHasher): Response
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
        $equipe = new Equipe;
        $notifications = new Notifications;
        $codeUiAll = new CodeUiAll;
        
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
                    if ($value->getNom()== $nom){
                        $message="Le nom assigné a l'équipe existe déjà!";            
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

                    $codeUiFind = $this->repoCodeUiAll->findAll();

                    if($codeUiFind==null){
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        $equipe->setCodeUi($random_code);
                        $codeUiAll->setCodeUi($random_code);
                        
                    }
                    foreach ($codeUiFind as $key => $value) {
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        if ($value->getCodeUi()==$random_code){
                            //pass
                        }
                        else if ($value->getCodeUi()!=$random_code){
                            $equipe->setCodeUi($random_code);
                            $codeUiAll->setCodeUi($random_code);
                        }

                    }

                    $this->em->persist($equipe);
                    $this->em->persist($codeUiAll);

                    $nombreEmploye = $this->repoEmploye->countEmploye();
                    $allEmploye = $this->repoEmploye->findAll();
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

                    /*$message="AFTER";            
                    return $this->json($message);*/

                    
                    $notifications->setTitre("Mise à jour Equipe");
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
                return $this->render('ajout_equipe.html.twig', [
                    'controller_name' => 'ClientController',
                    'employe'=>$employe
                ]);
            }
       

    }

    #[Route('/employe_register', name: 'app.employe.register')]
    public function employe_register(Request $request ,Security $security, UserPasswordHasherInterface $passwordHasher): Response
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

        $employe = new Employe;
        $utilisateur = new Utilisateur;
        $notifications = new Notifications;
        $codeUiAll = new CodeUiAll;
        
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
            $numero=$request->request->get('contact');
            $adresse=$request->request->get('adresse');
            $password=$request->request->get('password');
            $salaire=$request->request->get('salaire');
            $employeType=$request->request->get('employe');

            $numeroCheck=false;
            $emailCheck=false;
            $createdAt=Date('Y-m-d h:i');

            //dd("Nom:".$nom." Username:".$username." Mail:".$email." Numéro:".$numero." Password:".$password." Adresse:".$adresse." Payement:".$payement." Employe:".$employe." Salaire:".$salaire);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {
                if ($nom==null OR $username==null OR $numero==null OR $adresse==null OR $salaire==null){
                    
                    $message="Veuillez remplir tout les champs requis avant soumission";            
                    return $this->json($message);

                }
                if (strlen($numero)>8){
                    $message="Numéro de téléphone trop long!";            
                    return $this->json($message);     
                }

                if (strlen($salaire)>6 OR strlen($salaire)<5 OR ($salaire)<0){
                    $message="Le salaire est invalide";            
                    return $this->json($message);     
                }

                
                
                $clients = $this->utilisateurRepo->findDataClient();
                foreach ($clients as $key => $value) {
                    if ($value->getUsername()== $username){
                        $message="Le username existe déjà!";            
                        return $this->json($message);     
                    }
                    if ($value->getNumero()== $numero){
                        $message="Numéro de téléphone existant!";            
                        return $this->json($message);     
                    }
                }
                if (isset($nom,$username,$numero,$password,$adresse,$salaire,$employeType)){
                    $utilisateur->setNom($nom);
                    $utilisateur->setUsername($username);
                    //$utilisateur->setEmail($email);
                    $utilisateur->setNumero($numero);
                    $utilisateur->setAdresse($adresse);
                    $utilisateur->setCreatedAt($createdAt);
                    if($checkUser == false){
                        $utilisateur->setCreatedByGerant($gerantUser);
                        $employe->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $utilisateur->setCreatedByAdmin($admin);
                        $employe->setAdministrateur($admin);
                    }

                    $utilisateur->setEmailCheck($emailCheck);
                    $utilisateur->setNumeroCheck($numeroCheck);

                    if((int)$employeType==1){
                        $utilisateur->setRoles(["ROLE_REPASSEUR"]);
                        $employe->setRoles(["ROLE_REPASSEUR"]);
                    }

                    else if((int)$employeType==2){
                        $utilisateur->setRoles(["ROLE_LAVAGE"]);
                        $employe->setRoles(["ROLE_LAVAGE"]);
                    }
                    else if((int)$employeType==3){
                        $utilisateur->setRoles(["ROLE_LIVREUR"]);
                        $employe->setRoles(["ROLE_LIVREUR"]);
                    }

                    $hashedPassword = $passwordHasher->hashPassword($utilisateur,$password);
                    $utilisateur->setMotDePasse($hashedPassword);
                    $employe->setSalaire($salaire);


                    $codeUiFind = $this->repoCodeUiAll->findAll();
                    if($codeUiFind==null){
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        $employe->setCodeUi($random_code);
                        $codeUiAll->setCodeUi($random_code);
                        
                    }
                    foreach ($codeUiFind as $key => $value) {
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        if ($value->getCodeUi()==$random_code){
                            //pass
                        }
                        else if ($value->getCodeUi()!=$random_code){
                            $employe->setCodeUi($random_code);
                            $codeUiAll->setCodeUi($random_code);
                        }

                    }

                    $this->em->persist($employe);
                    $this->em->persist($codeUiAll);
                    $utilisateur->setEmploye($employe);
                    $this->em->persist($utilisateur);

                    $notifications->setTitre("Mise à jour Employe");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $notifications->setEmploye($employe);
                    $this->em->persist($notifications);
                    
                    $this->em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.liste.employe'),
                    ]);
                    
                }
            }
            else{
                return $this->render('register_employe.html.twig', [
                    'controller_name' => 'ClientController',
                ]);
            }
               
    }

    #[Route('/notfound', name: 'app.notfound')]
    public function notfound(): Response
    {  
        return $this->render('notfound.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

}
