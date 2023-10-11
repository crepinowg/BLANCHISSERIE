<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Facture;
use App\Entity\Rappel;
use Symfony\Component\Security\Core\Security;
use App\Controller\EmailSenderController;
use App\Entity\Livraison;
use App\Entity\Entete;
use App\Entity\Administrateur;
use App\Entity\Client;
use App\Entity\FactureEquipe;
use App\Controller\FunctionImplementController;
use App\Entity\Notifications;
use App\Repository\ClientRepository;
use App\Repository\EnteteRepository;
use App\Repository\TarifsRepository;
use App\Repository\EquipeRepository;
use App\Repository\AdministrateurRepository;
use App\Repository\FactureRepository;
use App\Repository\EmployeEquipeRepository;
use App\Repository\LivraisonRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\RappelRepository;
use App\Repository\FactureEquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\IconsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FactureController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;


    public function __construct(
        EntityManagerInterface $em,
        MailerService $mailerService,
        EmailSenderController $emailSender, 
        FactureEquipeRepository $factureEquipeRepo,
        FunctionImplementController $functionImplement,
        RappelRepository $rappelRepo,
        ClientRepository $clientRepo,
        EmployeEquipeRepository $employeEquipeRepo, 
        FactureRepository $factureRepo,
        TarifsRepository $tarifsRepo,
        EquipeRepository $equipeRepo,
        AuthorizationCheckerInterface $authorizationChecker,
        EnteteRepository $enteteRepo,
        IconsRepository $iconsRepo,
        LivraisonRepository $livraisonRepo,
        EntrepriseRepository $entrepriseRepo
        ){
        $this->clientRepo = $clientRepo;
        $this->mailerService = $mailerService;
        $this->emailSender = $emailSender;
        $this->employeEquipeRepo = $employeEquipeRepo;
        $this->rappelRepo = $rappelRepo;
        $this->factureEquipeRepo = $factureEquipeRepo;
        $this->factureRepo = $factureRepo;
        $this->functionImplement = $functionImplement;
        $this->tarifsRepo = $tarifsRepo;
        $this->equipeRepo = $equipeRepo;
        $this->enteteRepo = $enteteRepo;
        $this->iconsRepo = $iconsRepo;
        $this->livraisonRepo = $livraisonRepo;
        $this->entrepriseRepo = $entrepriseRepo;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route("/dataTarifs", name: 'app.data.tarifs')]
    public function sendData()
    {

        $tarifs = $this->tarifsRepo->findAll();
        $tarifsArray = [];
        $iconsArray = [];

        $tableau = array();

        foreach($tarifs as $key => $value) {

            $tableau[$key] = array(
                'id'=>$value->getId(),
                'prix' => $value->getPrix(),
                'type' => $value->getType(),
                'icon' => [
                    "id"=>$value->getIcons()->getId(),
                    "nom"=>$value->getIcons()->getNomIcon(),
                    "syntaxe"=>$value->getIcons()->getSyntaxeIcon(),
                ],
                'express' => $value->isExpress(),
                'admin' => $value->getAdmin(),
                
            );
        }
       
       /*foreach ($tarifs as $key => $value) {
            $tarifsArray[] = $value->toArray([
                'prix' => $value->getPrix(),
                'type' => $value->getType(),
                'icon' => [
                    "id"=>$value->getIcons()->getId(),
                    "nom"=>$value->getIcons()->getNomIcon(),
                    "syntaxe"=>$value->getIcons()->getSyntaxeIcon(),
                ],
                'express' => $value->isExpress(),
                'admin' => $value->getAdmin(),
                
            ]);
        }*/
        
        


       /* foreach ($tarifs as $key => $value) {
            
            $iconsArray[] = $key->toIconsArray([
                "id"=>$value->getIcons()->getId(),
                "nom"=>$value->getIcons()->getNomIcon(),
                "syntaxe"=>$value->getIcons()->getSyntaxeIcon(),
            ]);
        }

        $allArray = array([
            'tarifs'=>$tarifsArray,
            'icons'=>$iconsArray
        ]);*/

       //dd($tarifsArray);
        //$tarifsJson = json_encode($tarifs);
        $objet = array ([
            "nom" => "John",
            "age" => 30
        ]);
        return new JsonResponse($tableau);
    }

    #[Route("/rechercheFacture", name: 'app.recherche.facture')]
    public function rechercheFacture(Security $security)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $facture = $this->factureRepo->findAll();
        return $this->render('recherche_facture.html.twig', [
            
            'facture'=>$facture,
            
        ]);
    }

    #[Route("/dataFacture", name: 'app.data.facture')]
    public function sendDataFacture()
    {

        $facture = $this->factureRepo->findAll();
       
        $tableauFacture = array();

        foreach($facture as $key => $value) {

            $tableauFacture[$key] = array(
                'id'=>$value->getId(),
                'numeroE' => $value->getNumFacture(), 
            );
        }

      
        return new JsonResponse($tableauFacture);
    }


    #[Route('/facture_show-{id}', name: 'app.facture.show')]
    public function showFacture(ManagerRegistry $doctrine,Security $security,Request $request,Client $clients,int $id, PaginatorInterface $paginator)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $client = $this->clientRepo->find($id);
        $entreprise = $this->entrepriseRepo->find(1);
        $countFacture = $this->factureRepo->countFacture($id);
        $findFacture = $this->factureRepo->findFacture($id);
        if ($findFacture==null) {
            $countEntete=1;
        }
        else{
            foreach ($findFacture as $item => $value) {
                foreach ($value as $item1 => $value1) {
                
                $idFacture=$value1;     
                } 
            } 
            
            $countEntete = $this->enteteRepo->findEntete($idFacture);
        }
        
        $limit = $countEntete;
        $page = $request->query->getInt('page', 1);
        $livraison=$this->livraisonRepo->findClient($id,$page,1);
        $facture =$doctrine->getRepository(Facture::class)->jointureTable($id,$page,$limit);
        return $this->render('factureInvoice3.html.twig', [
            'clients'=>$clients,
            'client'=>$client,
            'facture'=>$facture,
            'livraison'=>$livraison,
            'countFacture'=>$countFacture,
            'limit'=>$limit,
            'page'=>$page,
            'entreprise'=>$entreprise
        ]);
    }

#[Route('/facture_showInvoice-{id}', name: 'app.facture.showInvoice')]
    public function showInvoice(ManagerRegistry $doctrine,Security $security,Request $request, Facture $facture, int $id,PaginatorInterface $paginator){

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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
        
        $client = $this->clientRepo->find($id);
        $entreprise = $this->entrepriseRepo->find(1);
        $countFacture = $this->factureRepo->countFacture($id);
        $equipeIdViaFacture=$this->factureEquipeRepo->equipeId($id);
        foreach ($equipeIdViaFacture as $key => $value) {
            $equipeId = $value->getEquipe()->getId();
        }
        $employeViaEmployeEquipe=$this->employeEquipeRepo->employeViaEmployeEquipe($equipeId);
        $findFacture = $this->factureRepo->findFacture($id);
        
        if ($findFacture==null){

            $countEntete=1;
        }
        else{
            foreach ($findFacture as $item => $value) {
                foreach ($value as $item1 => $value1) {
                
                $idFacture=$value1;     
                } 
            } 
            
            $countEntete = $this->enteteRepo->findEntete($idFacture);
        }
        
        $limit = $countEntete;
        $page = $request->query->getInt('page', 1);
        $livraison=$this->livraisonRepo->findClient($id,$page,1);
        $facture =$doctrine->getRepository(Facture::class)->find($id);
        //dd($facture);
        //$facture =$doctrine->getRepository(Facture::class)->jointureTable($id,$page,$limit);
        return $this->render('factureInvoice1.html.twig', [
            //'clients'=>$clients,
            //'client'=>$client,
            'facture'=>$facture,
            'livraison'=>$livraison,
            'countFacture'=>$countFacture,
            'limit'=>$limit,
            'page'=>$page,
            'entreprise'=>$entreprise,
            'employes'=>$employeViaEmployeEquipe,
            //'diffInDays'=>$diffInDays,
            //'diffInHours'=>$diffInHours
        ]);
    }

    #[Route('/factureShowEmploye-{id}', name: 'app.factureShowEmploye')]
    public function factureShowEmploye(ManagerRegistry $doctrine,Security $security,Request $request, Facture $facture, int $id,PaginatorInterface $paginator){

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_LAVAGE') OR ($this->authorizationChecker->isGranted('ROLE_LIVREUR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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
        
        $client = $this->clientRepo->find($id);
        $entreprise = $this->entrepriseRepo->find(1);
        $countFacture = $this->factureRepo->countFacture($id);
        $equipeIdViaFacture=$this->factureEquipeRepo->equipeId($id);
        foreach ($equipeIdViaFacture as $key => $value) {
            $equipeId = $value->getEquipe()->getId();
        }
        $employeViaEmployeEquipe=$this->employeEquipeRepo->employeViaEmployeEquipe($equipeId);
        $findFacture = $this->factureRepo->findFacture($id);
        
        if ($findFacture==null){

            $countEntete=1;
        }
        else{
            foreach ($findFacture as $item => $value) {
                foreach ($value as $item1 => $value1) {
                
                $idFacture=$value1;     
                } 
            } 
            
            $countEntete = $this->enteteRepo->findEntete($idFacture);
        }
        
        $limit = $countEntete;
        $page = $request->query->getInt('page', 1);
        $livraison=$this->livraisonRepo->findClient($id,$page,1);
        $facture =$doctrine->getRepository(Facture::class)->find($id);
        //dd($facture);
        //$facture =$doctrine->getRepository(Facture::class)->jointureTable($id,$page,$limit);
        return $this->render('factureInvoice1Employe.html.twig', [
            //'clients'=>$clients,
            //'client'=>$client,
            'facture'=>$facture,
            'livraison'=>$livraison,
            'countFacture'=>$countFacture,
            'limit'=>$limit,
            'page'=>$page,
            'entreprise'=>$entreprise,
            'employes'=>$employeViaEmployeEquipe,
            //'diffInDays'=>$diffInDays,
            //'diffInHours'=>$diffInHours
        ]);
    }



    
#[Route('/express_livrer-{id}', name: 'app.facture.express_livrer')]
    public function express_livrer(ManagerRegistry $doctrine,Security $security,Request $request, Entete $entete, int $id,PaginatorInterface $paginator){
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $entete = $this->enteteRepo->find($id);
        $entete->setExpressDelivered(1);
        $this->em->persist($entete);
        $this->em->flush();
        //dd($entete);
        $idFacture = $entete->getFacture()->getId();
        
    return $this->redirectToRoute('app.facture.showInvoice',["id"=>$idFacture]);
    
}


#[Route("/factureCreationn-{id}", name: 'app.facture.creationn')]
   
    public function creationn(Request $request,Security $security,Client $client,int $id,MailerInterface $mailer, ValidatorInterface $validator)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
            if ($suspendu == 1){
                return $this->redirectToRoute('app.security');
            }
            if ($statut == 1){
                return $this->redirectToRoute('app.logout');
            }
                if ($client->isDeleted() == 1){
                    return $this->redirectToRoute('app.notfound');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $facture = new Facture;
        $rappel = new Rappel;
        $livraison = new Livraison;
        $notifications = new Notifications;
        $factureEquipe = new FactureEquipe;

        $dateLivr=$request->request->get('dateLivr');
        $tauxReduc=$request->request->get('tauxReduc');
        $numFacture=$request->request->get('numFacture');
        $equipe=$request->request->get('equipe');
       
        $equipes= $this->equipeRepo->findAll();
        $tarifs= $this->tarifsRepo->findAll();
        $factureAll= $this->factureRepo->findAll();
        $createdAt = date("Y-m-d h:i");
        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }


            if ($request->isXmlHttpRequest()) {
                //dd("SHOWW");
                $loop = false;

                foreach ($factureAll as $key => $value) {
                    if($numFacture == null){
                        
                    }
                    else if($numFacture == $value->getNumFacture()){
                        $message="Numéro existant!";
                        $titre = "#01";
                        $tableauData= array();
                        $tableauData[]= $titre;
                        $tableauData[]= $message;
                    
                        return $this->json($tableauData);
                    }
                }

                if(strpos($numFacture, "-") !== false){
                    $message="Aucun caractère aurorisé!";
                    $titre = "#01";
                    $tableauData= array();
                    $tableauData[]= $titre;
                    $tableauData[]= $message;
                
                    return $this->json($tableauData);
                }

                if($dateLivr==null or $equipe==null){

                    $message="La date de livraison et l'assignation d'une équipe sont requises";
                    $titre = "#01";
                    $tableauData= array();
                    $tableauData[]= $titre;
                    $tableauData[]= $message;
                
                    return $this->json($tableauData);// Renvoie une réponse JSON
                }

                $timestamp = strtotime($dateLivr); // convertir la date en timestamp Unix
                $aujourdhui = time(); // timestamp Unix de la date et heure actuelles

                if ($timestamp < $aujourdhui) {
                    $message="Date de livraison invalide";
                    $titre = "#01";
                    $tableauData= array();
                    $tableauData[]= $titre;
                    $tableauData[]= $message;
                    return $this->json($tableauData);
                    $entete->setDateDeliveredExpress($dateExpress);
                } 

                if(isset($dateLivr, $equipe)){
                    
                    
                    $facture->setClient($client);

                if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
                        $checkUser=false;
                        $gerantUser = $this->getUser()->getGerant();
                    }
            
                    else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                        $checkUser=true;
                        $admin = $this->getUser()->getAdministrateur();
                    }

                    if($checkUser==false){
                        $facture->setGerant($gerantUser);
                    }
                    else if($checkUser==true){
                        $facture->setAdmin($admin);
                    }
                    if($tauxReduc!=null){
                        $facture->setTauxReduction($tauxReduc);
                    }
                    if($tauxReduc==null){
                        $facture->setTauxReduction(0);
                    }
                    if($numFacture!=null){
                        $facture->setNumFacture($numFacture);
                    }
                    
                    
                    $facture->setDateRecuperation(date("Y-m-d h:i"));
                    $facture->setDateLivraison($dateLivr); 
                    
                    
                    foreach ($equipes as $key => $value) {
                        //dd("OKAY1");
                        if ($value->getId() == $equipe) {
                            //dd("OKAY");
                            $factureEquipe->setFacture($facture);
                            $factureEquipe->setEquipe($value);
                            $this->em->persist($factureEquipe);
                        }
                    }
                    //dd("NOO");
                    
                    $prixTotal=0;
                    $factureExpress=false;                
                
                    for ($i=1; $i <10 ; $i++) { 
                        
                        $entete='entete'.$i;
                        $entete = new Entete;                   
                        //$question = $request->request->get('question'.$i);

                        $qte=$request->request->get('qte'.$i);
                    // $description=$request->request->get('description'.$i);
                        //$pu=(int)$request->request->get('pu'.$i);
                        $pt=(int)$request->request->get('pt'.$i);
                        $express=$request->request->get('expressHidden'.$i);
                        $dateExpress=$request->request->get('dateExpress'.$i);
                        $tarifId=(int)$request->request->get('idTarifs'.$i);
                        //$pt=0;
                        
                        if($qte==null and $tarifId!=null){

                            $message="Le champ 'Quantité' est requis pour poursuivre la soumission";
                            $identifiant = $i;
                            $tableauData= array();
                            $tableauData[]= $identifiant;
                            $tableauData[]= $message;
                        
                            return $this->json($tableauData); // Renvoie une réponse JSON
                        }

                        else if($qte!=null and $tarifId==null){

                            $message="Choisissez un vêtement et son service pour continuer";
                            $identifiant = $i;
                            $tableauData= array();
                            $tableauData[]= $identifiant;
                            $tableauData[]= $message;
                            return $this->json($tableauData); // Renvoie une réponse JSON
                        }
                        else if($qte==0){

                            $message="Quantité invalide";
                            $identifiant = $i;
                            $tableauData= array();
                            $tableauData[]= $identifiant;
                            $tableauData[]= $message;
                            return $this->json($tableauData); // Renvoie une réponse JSON
                        }
                        //dd($express."-".$qte."-".$tarifId."-".$pt);
                        
                        if($qte!=null and $tarifId!=null and $pt!=null){
                            $loop= true;
                        
                            if($qte>0){

                                $entete->setFacture($facture);
                                $entete->setQuantite($qte);
                                //$entete->setDescription($description);
                                //$entete->setPrixUnitaire($pu);
                                //$pt=$pu*$qte;
                                $entete->setPrixTotal($pt);
                                foreach ($tarifs as $key => $value) {
                                    if ($value->getId() == $tarifId) {
                                        $entete->setTarifs($value);
                                    }
                                }
                                
                                //dd($express."-".$dateExpress);
                                if(isset($express)){
                                    
                                    if($express==0){
                                        $entete->setExpress(false);
                                    }
                                    
                                    elseif($express==1){
                                        $entete->setExpress(true);
                                        if($dateExpress==null){
                                            $message="La date de livraison express est requise.";
                                            $identifiant = $i;
                                            $tableauData= array();
                                            $tableauData[]= $identifiant;
                                            $tableauData[]= $message;
                                            return $this->json($tableauData); 
                                        }
                                        $timestamp = strtotime($dateExpress); // convertir la date en timestamp Unix
                                        $aujourdhui = time(); // timestamp Unix de la date et heure actuelles

                                        if ($timestamp < $aujourdhui) {
                                            $message="Date de livraison express invalide";
                                            $identifiant = $i;
                                            $tableauData= array();
                                            $tableauData[]= $identifiant;
                                            $tableauData[]= $message;
                                            return $this->json($tableauData);
                                            $entete->setDateDeliveredExpress($dateExpress);
                                        }

                                        if ($timestamp > $aujourdhui) {
                                        
                                            $entete->setDateDeliveredExpress($dateExpress);
                                        } 
                                        
                                        $factureExpress=true;
                                    }
                                    
                                    
                                    $entete->setExpressDelivered(0);
                                    
                                }
    
                            }
                            
                            $this->em->persist($entete);
                            //$this->em->flush();
                            //dump($entete);
                            
                        }
                        else if($loop == false){
                            $message="Créer au moins une désignation pour générer la facture";
                            $titre = "#01";
                            $tableauData= array();
                            $tableauData[]= $titre;
                            $tableauData[]= $message;
                        
                            return $this->json($tableauData);
                        }
                        $prixTotal=$prixTotal+$pt;
                    }

                    
                    
                    

                    if($tauxReduc<=0){
                        $prixTva=$prixTotal;
                    }
                    else if($tauxReduc==null){
                        $prixTva=$prixTotal;
                    }
                    else{
                        $prixTva=$prixTotal-(($prixTotal*$tauxReduc)/100);
                    }
                    
                    $facture->setTotalTtc($prixTotal);
                    $facture->setTotalTva($prixTva);
                    $factureAll = $this->factureRepo->findAll();
                    if($factureAll==null){
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        $facture->setFactureIdNumber($random_code);

                        $random_codeInvoice = substr(str_shuffle($chars), 0, 4);
                        $facture->setInvoiceCode($random_codeInvoice);
    
                    }
                    
                    foreach ($factureAll as $key => $value) {
                        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $random_code = substr(str_shuffle($chars), 0, 4);
                        if ($value->getFactureIdNumber()==$random_code){
                            //pass
                        }
                        else if ($value->getFactureIdNumber()!=$random_code){
                            $facture->setFactureIdNumber($random_code);
                        }

                    }
                    foreach ($factureAll as $key => $value) {
                        $charsInvoice = "0123456789";
                        $random_code = substr(str_shuffle($charsInvoice), 0, 4);
                        if ($value->getInvoiceCode()==$random_code){
                            //pass
                        }
                        else if ($value->getInvoiceCode()!=$random_code){
                            $facture->setInvoiceCode($random_code);
                        }

                    }

                    $facture->setEtat("LAVAGE");
                    if($factureExpress==true){
                        $facture->setExpress(1);
                    }
                    else if($factureExpress==false){
                        $facture->setExpress(0);
                    }
                    

                    //$this->em->persist($livraison);
                // $facture->setLivraison($livraison);
                    
                    
                    $notifications->setTitre("Mise à jour Facture");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("MJ");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $this->em->persist($facture);

                    $rappel->setNom("Livraison Programée");
                    
                    $rappel->setDescription("Cette facture doit être livré a son propriétaire aujourd'hui.");
                    
                    $rappel->setCreatedAt($createdAt);

                    $date = new \DateTime($dateLivr);
                    $dateSansHeure = $date->format('Y-m-d');

                    $heureLivraison = new \DateTime($dateLivr);
                    $dateAvecHeure = $heureLivraison->format('h:i');

                    $rappel->setJourAt(date('Y-m-d'));
                    $rappel->setHeureAt(date('h:i'));
                    $rappel->setDateFinAt($dateSansHeure);
                    $rappel->setHeureFinAt($dateAvecHeure);
                    $rappel->setTypeRappel(3);
                    $rappel->setFacture($facture);
                    
                    if($checkUser == false){
                        $rappel->setCreatedByGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $rappel->setCreatedByAdmin($admin);
                    }

                    $this->em->persist($rappel);
                    $notifications->setFacture($facture);
                    $this->em->persist($notifications);
                    
                
                    $this->em->flush();

                    // Validez les données du formulaire ici
                /* $errors = $validator->validate($this->em->flush());

                    if (count($errors) > 0) {
                        // Retourne une réponse JSON contenant les erreurs
                        $errorMessages = [];
                        foreach ($errors as $error) {
                            $errorMessages[] = $error->getMessage();
                        }

                        return new JsonResponse([
                            'success' => false,
                            'errors' => $errorMessages,
                        ], 500);
                    }*/


                // return $this->json("TTTTTTTTTTT");
                // header("Location:app.facture.show', ['id' => $id]");
                    //exit;
                    //return new RedirectResponse('app.facture.show', ['id' => $id]);
                    // Effectue une redirection vers la page de redirection
                   
                    
                    //return $this->redirectToRoute('app.facture.show', ['id' => $id]);
                   $today = Date("d-M-Y");
                   foreach ($client->getUtilisateur() as $key => $value) {
                     $user = $value;
                   }
                   if($user->isIsVerify()){
                    $this->mailerService->sendEmailFacture(
                        $user->getEmail(),
                        "Facture du ".$today,
                        "mail_facture.html.twig",
                        [
                            "client"=>$client
                        ]
                        
                        );
                   }

                /* $email = (new Email())
                    ->from('toviawoukplolacrepin@gmail.com')
                    ->to('crepintoviawou@gmail.com')
                    ->subject('Time for Symfony Mailer!')
                    ->text('Sending emails is fun again!')
                    ->html('<p>COUCOU</p>')
                    ;

                    dd($mailer);
                    //$transports = Transport::fromDns();
                //dd($mailer= new Mailer());
                ($mailer->send($email));*/

                return new JsonResponse([
                    'success' => true,
                    'redirect_url' => $this->generateUrl('app.facture.show', ['id' => $id]),
                ]);

                }

                else{
                    $icons = $this->iconsRepo->findAll();
                    $tarifs = $this->tarifsRepo->findAll();
                    $tableau = array();

                    return $this->render('creation-facture.html.twig', [
                        'controller_name' => 'FactureController',
                        'factureCreation'=>$client,
                        'tarifs'=>$tarifs,
                        'equipes'=>$equipes,
                        'icons'=>$icons,
                    ]);

                }
            }

            else {
                $icons = $this->iconsRepo->findAll();
                $tarifs = $this->tarifsRepo->findAll();
                
                return $this->render('creation-facture.html.twig', [
                    'controller_name' => 'FactureController',
                    'factureCreation'=>$client,
                    'tarifs'=>$tarifs,
                    'equipes'=>$equipes,
                    'icons'=>$icons,
                ]);
            }
    
}



#[Route("/facture", name: 'app.facture')]
   
    public function facture(Request $request,Security $security)
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $client = $this->clientRepo->findAll();
        
        return $this->render('facture.html.twig', [
                'controller_name' => 'FactureController',
                'client'=>$client,
            ]);

    }

    #[Route("/statutFacture-{id}", name: 'app.statut.facture')]
   
    public function statutFacture(Request $request, Facture $facture,int $id,Security $security)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $facture->setEtat('ATTENTE');
        $c=$facture->getClient()->getId();

        $notifications = new Notifications;
        
        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
            
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }

       //$fatureObjet=$this->factureRepo->fatureObjet($facture);
        //dd($factureLivraison);
        

        $notifications->setTitre("Attente de Livraison");
        $notifications->setReader(false);
        $notifications->setCreatedAt(date("Y-m-d h:i"));
        $notifications->setTypeNotif("OTHER");
        if($checkUser == false){
            $notifications->setGerant($gerantUser);
        }

        else if($checkUser == true){
            $notifications->setAdmin($admin);
        }
        $notifications->setFacture($facture);

        $this->em->persist($notifications);
        //dd($facture);
        $this->em->persist($facture);
        $this->em->flush();
        return $this->redirectToRoute('app.facture.show', ['id' => $c]);

    }

    #[Route("/statut2Facture-{id}", name: 'app.statut2.facture')]
   
    public function statut2Facture(Request $request, Facture $facture,int $id,Security $security)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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

        $facture->setEtat('LIVRAISON');
        $facture->setDeliveredAt(date("y-m-d h:i"));
        
        $c=$facture->getClient()->getId();

       

        $thisFacture = $this->factureRepo->thisFacture($id);
        $diffInDays = "N/A";
        foreach ($thisFacture as $key => $value) {
            $dateCollecte = new \DateTime($value->getDateRecuperation());
            $dateLivraison = new \DateTime($value->getDeliveredAt());
            if($value->getDeliveredAt()==""){
                $diffInDays = 0;
            }
            else{
                $diff = $dateCollecte->diff($dateLivraison);
                $diffInDays = $diff->days;
                $diffInHours = $diffInDays*24;
                $facture->setJoursPasser($diffInDays);
                $facture->setHeurePasser($diffInHours);
            }
            
    
           
        }

        $entetes = $this->enteteRepo->findEnteteByFacture($id);

        foreach ($entetes as $key => $value) {
            if($value->isExpress() == 1){
                $value->setExpressDelivered(1);
                $this->em->persist($value);
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

        $notifications->setTitre("Livraison Check");
        $notifications->setReader(false);
        $notifications->setCreatedAt(date("Y-m-d h:i"));
        $notifications->setTypeNotif("OTHER");
        if($checkUser == false){
            $notifications->setGerant($gerantUser);
        }

        else if($checkUser == true){
            $notifications->setAdmin($admin);
        }
        $notifications->setFacture($facture);

        $this->em->persist($facture);
        $this->em->persist($notifications);
        $this->em->flush();
        return $this->redirectToRoute('app.facture.showInvoice', ['id' => $id]);

    }

    #[Route("/factureExpressLivre-{id}", name: 'app.factureExpressLivre')]
   
    public function factureExpressLivre(Request $request, Entete $entete,int $id,Security $security)
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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
        
        $entetes = $this->enteteRepo->findEnteteById($id);
        foreach ($entetes as $key => $value){
            $value->setExpressDelivered(1);
            $this->em->persist($value);
            $c=$value->getFacture()->getClient()->getId();
        }
        
        
        $this->em->flush();
        return $this->redirectToRoute('app.facture.show', ['id' => $c]);

    }

    #[Route("/countEnteteExpress-{id}", name: 'app.countEnteteExpress')]
   
    public function countEnteteExpress(Request $request, Facture $facture,int $id,Security $security)
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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
        
        $entetes = $this->enteteRepo->countEnteteByFatureId($id);
        foreach ($entetes as $key => $value){
            $value->setExpressDelivered(1);
            $this->em->persist($value);
            $c=$value->getFacture()->getClient()->getId();
        }
        
        
        $this->em->flush();
        return $this->redirectToRoute('app.facture.show', ['id' => $c]);

    }

    
#[Route("/facture_update-{id}", name: 'app.facture.update')]
   
public function facture_update(Request $request,Facture $facture,int $id,MailerInterface $mailer,Security $security, ValidatorInterface $validator)
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

            if ($facture->getEtat() == "LIVRAISON"){
                return $this->redirectToRoute('app.notfound');
            }

        }
        else{
            return $this->redirectToRoute('app.notfound');           
        }
    }
    
    $livraison = new Livraison;
    $rappel = new Rappel;
    $notifications = new Notifications;
    $factureEquipe = new FactureEquipe;

    $dateLivr=$request->request->get('dateLivr');
    $tauxReduc=$request->request->get('tauxReduc');
    $numFacture=$request->request->get('numFacture');
    $equipe=$request->request->get('equipe');
   
    $equipes= $this->equipeRepo->findAll();
    $factureEquipes= $this->factureEquipeRepo->equipeId($id);
    $tarifs= $this->tarifsRepo->findAll();
    $factureAll= $this->factureRepo->findAll();
    
    if ($request->isXmlHttpRequest()) {
        //dd("SHOWW");
        $loop = false;

        foreach ($factureAll as $key => $value) {
            if($numFacture == null){
                
            }
            else if($numFacture == $value->getNumFacture() && $value->getId()!=$id){
                $message="Numéro existant!";
                $titre = "#01";
                $tableauData= array();
                $tableauData[]= $titre;
                $tableauData[]= $message;
            
                return $this->json($tableauData);
            }
        }

        if(strpos($numFacture, "-") !== false){
            $message="Aucun caractère aurorisé!";
            $titre = "#01";
            $tableauData= array();
            $tableauData[]= $titre;
            $tableauData[]= $message;
        
            return $this->json($tableauData);
        }

        if($dateLivr==null or $equipe==null){

            $message="La date de livraison et l'assignation d'une équipe sont requises";
            $titre = "#01";
            $tableauData= array();
            $tableauData[]= $titre;
            $tableauData[]= $message;
        
            return $this->json($tableauData);// Renvoie une réponse JSON
        }

        $timestamp = strtotime($dateLivr); // convertir la date en timestamp Unix
        $aujourdhui = time(); // timestamp Unix de la date et heure actuelles

        if ($timestamp < $aujourdhui) {
            $message="Date de livraison invalide";
            $titre = "#01";
            $tableauData= array();
            $tableauData[]= $titre;
            $tableauData[]= $message;
            return $this->json($tableauData);
            $entete->setDateDeliveredExpress($dateExpress);
        } 

        if(isset($dateLivr, $equipe)){
            
            
            //$facture->setClient($client);

           if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
                $checkUser=false;
                $gerantUser = $this->getUser()->getGerant();
            }
    
            else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                $checkUser=true;
                $admin = $this->getUser()->getAdministrateur();
            }

            if($checkUser==false){
                $facture->setGerant($gerantUser);
            }
            else if($checkUser==true){
                $facture->setAdmin($admin);
            }
            if($tauxReduc!=null){
                $facture->setTauxReduction($tauxReduc);
            }
            if($tauxReduc==null){
                $facture->setTauxReduction(0);
            }
            if($numFacture!=null){
                $facture->setNumFacture($numFacture);
            }
            
            
            //$facture->setDateRecuperation(date("Y-m-d h:i"));
            $facture->setDateLivraison($dateLivr); 
                //dd("OKAY1");
                    
                foreach ($equipes as $key => $value) {
                    if ($value->getId() == $equipe) {
                        foreach ($factureEquipes as $key => $values) {
                            $values->setEquipe($value);
                            $this->em->persist($values);
                        }
                    }
                }
            
            //dd("NOO");
            $facturePrixTotal= $this->factureRepo->find($id);
            $prixTotal = $facture->getTotalTtc();
            /*foreach ($facturePrixTotal as $key => $value) {
                $prixTotal = $value->getTotalTtc();
            }*/
            
            $factureExpress=false;                
            /*foreach ($facture->getEntete() as $key => $value) {

                $entity = $this>enteteRepo()->find($value->getId());

                $this->em->remove($entity);
                
            }*/
            //$this->em->flush();
            
           
            for ($i=1; $i <10 ; $i++) { 
               
                $entete='entete'.$i;
                $entete = new Entete;                   
                //$question = $request->request->get('question'.$i);

                $qte=$request->request->get('qte'.$i);
            // $description=$request->request->get('description'.$i);
                //$pu=(int)$request->request->get('pu'.$i);
                $pt=(int)$request->request->get('pt'.$i);
                $express=$request->request->get('expressHidden'.$i);
                $dateExpress=$request->request->get('dateExpress'.$i);
                $tarifId=(int)$request->request->get('idTarifs'.$i);
                //$pt=0;
                
                if($qte==null and $tarifId!=null){

                    $message="Le champ 'Quantité' est requis pour poursuivre la soumission";
                    $identifiant = $i;
                    $tableauData= array();
                    $tableauData[]= $identifiant;
                    $tableauData[]= $message;
                
                    return $this->json($tableauData); // Renvoie une réponse JSON
                }

                else if($qte!=null and $tarifId==null){

                    $message="Choisissez un vêtement et son service pour continuer";
                    $identifiant = $i;
                    $tableauData= array();
                    $tableauData[]= $identifiant;
                    $tableauData[]= $message;
                    return $this->json($tableauData); // Renvoie une réponse JSON
                }
                else if($qte==0){

                    $message="Quantité invalide";
                    $identifiant = $i;
                    $tableauData= array();
                    $tableauData[]= $identifiant;
                    $tableauData[]= $message;
                    return $this->json($tableauData); // Renvoie une réponse JSON
                }
                //dd($express."-".$qte."-".$tarifId."-".$pt);
                
                if($qte!=null and $tarifId!=null and $pt!=null){
                    $loop= true;
                   
                    if($qte>0){

                        $entete->setFacture($facture);
                        $entete->setQuantite($qte);
                        //$entete->setDescription($description);
                        //$entete->setPrixUnitaire($pu);
                        //$pt=$pu*$qte;
                        $entete->setPrixTotal($pt);
                        foreach ($tarifs as $key => $value) {
                            if ($value->getId() == $tarifId) {
                                $entete->setTarifs($value);
                                
                        
                        //dd($express."-".$dateExpress);
                            
                            
                            if($express == 0){  
                                $entete->setExpress(false);
                                $entete->setExpressDelivered(0);
                            }
                            else if($express == 1){
                              
                                $entete->setExpress(true);
                                if($dateExpress==null){
                                    $message="Veuillez saisir la date de livraison express";
                                    $identifiant = $i;
                                    $tableauData= array();
                                    $tableauData[]= $identifiant;
                                    $tableauData[]= $message;
                                    return $this->json($tableauData); 
                                }
                                $timestamp = strtotime($dateExpress); // convertir la date en timestamp Unix
                                $aujourdhui = time(); // timestamp Unix de la date et heure actuelles

                                if ($timestamp < $aujourdhui ) {
                                    $message="Date de livraison express invalide";
                                    $identifiant = $i;
                                    $tableauData= array();
                                    $tableauData[]= $identifiant;
                                    $tableauData[]= $message;
                                    return $this->json($tableauData);
                                    $entete->setDateDeliveredExpress($dateExpress);
                                }

                                if ($timestamp > $aujourdhui) {
                                
                                    $entete->setDateDeliveredExpress($dateExpress);
                                } 
                                
                                $factureExpress=true;
                            
                            
                            
                            $entete->setExpressDelivered(0);
                            
                        }
                    }
                }

                    }
                    $prixTotal=$prixTotal+$pt;
                    $this->em->persist($entete);
                    
                    //$this->em->flush();
                    //dump($entete);
                    
                }
                
                else if($loop == false){
                    if($tauxReduc<=0){
                        $prixTva=$prixTotal;
                    }
                    else if($tauxReduc==null){
                        $prixTva=$prixTotal;
                    }
                    else{
                        $prixTva=$prixTotal-(($prixTotal*$tauxReduc)/100);
                    }
                    $facture->setTotalTtc($prixTotal);
                    $facture->setTotalTva($prixTva);
                    $notifications->setTitre("Update Info Facture");
                    $notifications->setReader(false);
                    $notifications->setCreatedAt(date("Y-m-d h:i"));
                    $notifications->setTypeNotif("UP");
                    if($checkUser == false){
                        $notifications->setGerant($gerantUser);
                    }
        
                    else if($checkUser == true){
                        $notifications->setAdmin($admin);
                    }
                    $this->em->persist($facture);

                    $rappelByFacture = $this->rappelRepo->rappelByFacture($id);
                    foreach ($rappelByFacture as $key => $value) {
                        $value->setNom("Livraison Programée");
                    
                        $value->setDescription("Cette facture doit être livré a son propriétaire aujourd'hui.");
                        
                        $value->setCreatedAt(date("Y-m-d h:i"));
            
                        $date = new \DateTime($dateLivr);
                        $dateSansHeure = $date->format('Y-m-d');
            
                        $heureLivraison = new \DateTime($dateLivr);
                        $dateAvecHeure = $heureLivraison->format('h:i');
            
                        $value->setJourAt(date('Y-m-d'));
                        $value->setHeureAt(date('h:i'));
                        $value->setDateFinAt($dateSansHeure);
                        $value->setHeureFinAt($dateAvecHeure);
                        $value->setTypeRappel(3);
                        $value->setFacture($facture);
                        
                        if($checkUser == false){
                            $value->setCreatedByGerant($gerantUser);
                        }
            
                        else if($checkUser == true){
                            $value->setCreatedByAdmin($admin);
                        }
            
                        $this->em->persist($value);
                    }
                    
                    $notifications->setFacture($facture);
                    $this->em->persist($notifications);
                    
                    $this->em->flush();

                    $today = Date("d-M-Y");
                    foreach ($facture->getClient()->getUtilisateur() as $key => $value) {
                      $user = $value;
                    }
                    if($user->isIsVerify()){
                        $this->mailerService->sendEmailFacture(
                        $user->getEmail(),
                        "Mise à jour de la facture du ".$facture->getDateRecuperation(),
                        "mail_facture.html.twig",
                        [
                            "client"=>$facture->getClient(),
                            "username"=>$user->getUsername(),
                        ]
                        
                        );
                    }
 
                   
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.facture.show', ['id' => $facture->getClient()->getId()]),
                    ]);
                }
                
               
            }

            

            if($tauxReduc<=0){
                $prixTva=$prixTotal;
            }
            else if($tauxReduc==null){
                $prixTva=$prixTotal;
            }
            else{
                $prixTva=$prixTotal-(($prixTotal*$tauxReduc)/100);
            }
            
            $facture->setTotalTtc($prixTotal);
            $facture->setTotalTva($prixTva);


            //$facture->setEtat("LAVAGE");
            if($factureExpress==true){
                $facture->setExpress(1);
            }
            else if($factureExpress==false){
                $facture->setExpress(0);
            }
            

            //$this->em->persist($livraison);
        // $facture->setLivraison($livraison);
            
            
            $notifications->setTitre("Update Facture");
            $notifications->setReader(false);
            $notifications->setCreatedAt(date("Y-m-d h:i"));
            $notifications->setTypeNotif("UP");
            if($checkUser == false){
                $notifications->setGerant($gerantUser);
            }

            else if($checkUser == true){
                $notifications->setAdmin($admin);
            }
            $this->em->persist($facture);

            $rappel->setNom("Livraison Programée");
            
            $rappel->setDescription("Cette facture doit être livré a son propriétaire aujourd'hui.");
            
            $rappel->setCreatedAt(date("Y-m-d h:i"));

            $date = new \DateTime($dateLivr);
            $dateSansHeure = $date->format('Y-m-d');

            $heureLivraison = new \DateTime($dateLivr);
            $dateAvecHeure = $heureLivraison->format('h:i');

            $rappel->setJourAt(date('Y-m-d'));
            $rappel->setHeureAt(date('h:i'));
            $rappel->setDateFinAt($dateSansHeure);
            $rappel->setHeureFinAt($dateAvecHeure);
            $rappel->setTypeRappel(3);
            $rappel->setFacture($facture);
            
            if($checkUser == false){
                $rappel->setCreatedByGerant($gerantUser);
            }

            else if($checkUser == true){
                $rappel->setCreatedByAdmin($admin);
            }

            $this->em->persist($rappel);
            $notifications->setFacture($facture);
            $this->em->persist($notifications);
            
            $this->em->flush();

            $today = Date("d-M-Y");
            foreach ($facture->getClient()->getUtilisateur() as $key => $value) {
              $user = $value;
            }
            if($user->isIsVerify()){
                $this->mailerService->sendEmailFacture(
                    $user->getEmail(),
                    "Mise à jour de la facture du ".$facture->getDateRecuperation(),
                    "mail_facture.html.twig",
                    [
                       "client"=>$facture->getClient(),
                       "username"=>$user->getUsername(),
                   ]
                    
                    );
            }
            

            
            // Validez les données du formulaire ici
           /* $errors = $validator->validate($this->em->flush());

            if (count($errors) > 0) {
                // Retourne une réponse JSON contenant les erreurs
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return new JsonResponse([
                    'success' => false,
                    'errors' => $errorMessages,
                ], 500);
            }*/


           // return $this->json("TTTTTTTTTTT");
           // header("Location:app.facture.show', ['id' => $id]");
            //exit;
            //return new RedirectResponse('app.facture.show', ['id' => $id]);
            // Effectue une redirection vers la page de redirection
            return new JsonResponse([
                'success' => true,
                'redirect_url' => $this->generateUrl('app.facture.show', ['id' => $facture->getClient()->getId()]),
            ]);
            
            //return $this->redirectToRoute('app.facture.show', ['id' => $id]);

        /* $email = (new Email())
            ->from('toviawoukplolacrepin@gmail.com')
            ->to('crepintoviawou@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>COUCOU</p>')
            ;

            dd($mailer);
            //$transports = Transport::fromDns();
        //dd($mailer= new Mailer());
        ($mailer->send($email));*/

        }

        else{
            $icons = $this->iconsRepo->findAll();
            $tarifs = $this->tarifsRepo->findAll();
            $tableau = array();

            return $this->render('creation-facture.html.twig', [
                'controller_name' => 'FactureController',
                'factureCreation'=>$client,
                'tarifs'=>$tarifs,
                'equipes'=>$equipes,
                'icons'=>$icons,
            ]);

        }
    }

    else {
        $icons = $this->iconsRepo->findAll();
        $tarifs = $this->tarifsRepo->findAll();
        
        return $this->render('update_facture.html.twig', [
            'controller_name' => 'FactureController',
            'factureCreation'=>$facture->getClient(),
            'tarifs'=>$tarifs,
            'facture'=>$facture,
            'equipes'=>$equipes,
            'icons'=>$icons,
        ]);
      }
    dd("SHOWZZ");
}

#[Route('/entete_supprimer-{id}', name: 'app.entete.supprimer')]
    public function entete_supprimer(Entete $entete, int $id,Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

            $suspendu = $this->functionImplement->admin_suspendu();
            $statut = $this->functionImplement->gerant_suspendu();
            //dd($statut);
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


        $entete->setStatut(true);
            $newTotalTva = $entete->getFacture()->getTotalTva() - $entete->getPrixTotal();
            $newTotalTtc = $entete->getFacture()->getTotalTtc() - $entete->getPrixTotal();
            $entete->getFacture()->setTotalTva($newTotalTva);
            $entete->getFacture()->setTotalTtc($newTotalTtc);
            $this->em->persist($entete->getFacture());
        

        $this->em->persist($entete);
        $this->em->flush();
        return $this->redirectToRoute('app.facture.showInvoice', ["id"=>$entete->getFacture()->getId()]);
       
    }
    
    
       
}

