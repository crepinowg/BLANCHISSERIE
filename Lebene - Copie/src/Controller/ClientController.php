<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Security\Core\Security;
use App\Repository\UtilisateurRepository;
use App\Repository\FactureRepository;
use App\Repository\EnteteRepository;
use App\Repository\LivraisonRepository;
use App\Repository\AdministrateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Entete;
use App\Entity\Facture;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\FunctionImplementController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientController extends AbstractController
{
    public function __construct(
        ClientRepository $clientRepo , 
        UtilisateurRepository $utilisateurRepo , 
        EnteteRepository $enteteRepo,
        FactureRepository $factureRepo ,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        AdministrateurRepository $adminRepo, 
        LivraisonRepository $livraisonRepo,
        FunctionImplementController $functionImplement
        ){
        $this->em = $em;
        $this->clientRepo = $clientRepo;
        $this->adminRepo = $adminRepo;
        $this->utilisateurRepo = $utilisateurRepo;
        $this->factureRepo = $factureRepo;
        $this->authorizationChecker = $authorizationChecker;
        $this->enteteRepo = $enteteRepo;
        $this->livraisonRepo = $livraisonRepo;
        $this->functionImplement = $functionImplement;
    }
    /**
     * @param Client $client
     */
    #[Route('/client', name: 'app.client')]
    public function index(Security $security): Response
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

            $client = $this->clientRepo->findAll();
            $countB = $this->clientRepo->countB();
            $countS = $this->clientRepo->countS();
            $countG = $this->clientRepo->countG();
            $countD = $this->clientRepo->countD();
            $countN = $this->clientRepo->countN();
            $countClient = $this->clientRepo->countClient();
            if($countClient==0){
                $pB=100*$countB;
                $pS=100*$countS;
                $pG=100*$countG;
                $pD=100*$countD;
                $pN=100*$countN;
            }
            else{
                $pB=100*$countB/$countClient;
                $pS=100*$countS/$countClient;
                $pG=100*$countG/$countClient;
                $pD=100*$countD/$countClient;
                $pN=100*$countN/$countClient;
            }
            
            $r1=0;
            $r2=0;
            $r3=0;
            
            //$factures=$this->factureRepo->best();
           /* foreach ($factures as $item => $value){
                $id=$value->getClient()->getId();

                $factureCountId=$this->factureRepo->countFacture($id);
                if ($factureCountId>$r1) {
                    $r1=$factureCountId;
                    $idr1=$id;
                    
                }
                else if ($factureCountId>$r1 && $factureCountId>$r2  ) {
                    $r2=$factureCountId;
                    $idr2=$id;
                }

                else if ($factureCountId>$r1 && $factureCountId>$r2 && $factureCountId>$r3 ) {
                    $r3=$factureCountId;
                    $idr3=$id;
                    
                }
                

            }*/
            /*if($r1!=0 && $r2!=0 && $r3!=0){
                $findBest = $this->clientRepo->findBest($idr1,$idr2,$idr3);
            }
            else{
                $findBest=1;
            }*/

            $findBest = $this->clientRepo->findBest();
            $findBest2 = $this->clientRepo->findBest2();

            return $this->render('dashboards-commerce.html.twig', [
                'controller_name' => 'ClientController',
                'countB'=>$countB,
                'countS'=>$countS,
                'countG'=>$countG,
                'countD'=>$countD,
                'pB'=>$pB,
                'pS'=>$pS,
                'pG'=>$pG,
                'pD'=>$pD,
                'pN'=>$pN,
                "findBest"=>$findBest,
                "findBest2"=>$findBest2
            ]);
       
    }

   /* #[Route('/client_ajout', name: 'app.client.ajout')]
    public function ajout(Request $request): Response
    {

        $client = new Client;
        $nom=$request->request->get('nom');
        $username=$request->request->get('username');
        $email=$request->request->get('email');
        $sexe=$request->request->get('sexe');
        $adresse=$request->request->get('adresse');
        $zip=$request->request->get('zip');
        $indication=$request->request->get('indication');
        $contact=$request->request->get('contact');

        if ($request->isXmlHttpRequest()) {

            if(isset($nom,$email,$sexe,$adresse,$username,$zip,$indication,$contact)){
                $client->setNom($nom);
                $client->setEmail($email);
                $client->setSexe($sexe);
                $client->setAdresse($adresse);
                $client->setZip($zip);
                $client->setContact($contact);
                $client->setIndication($indication);
                $client->setStatut("RAS");
                $this->em->persist($client);
                $this->em->flush();
            }
        }

      else{
            $client = $this->clientRepo->findAll();
        dump($client);
             return $this->render('forms-wizard.html.twig', [
            'controller_name' => 'ClientController',
        ]);

        }

        return $this->redirectToRoute('app.client.liste');

        
    }*/

    #[Route("/dataClient", name: 'app.data.client')]
    public function sendDataClient()
    {

        $client = $this->utilisateurRepo->findDataClient();
       
        $tableauClient = array();

        foreach($client as $key => $value) {

            $tableauClient[$key] = array(
                'id'=>$value->getId(),
                'email' => $value->getEmail(),
                'numero' => $value->getNumero(),
                'username' => $value->getUsername(),
                
            );
            
        }

      
        return new JsonResponse($tableauClient);
    }




    #[Route('/client_liste', name: 'app.client.liste')]
    public function liste(Request $request,Security $security)
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

        $client = $this->clientRepo->findAll();
        $touteLesFactures  =$this->factureRepo->allFacture();

        $this->functionImplement->gradeClient($client,$touteLesFactures);
        //dd("RAS");
        $this->addFlash('NoClient','Aucun client enregistrer. Ajouter  pour commencer');
        return $this->render('tables-grid.html.twig', [
            'client'=>$client,
            'touteLesFactures'=>$touteLesFactures
        ]);
    }

    #[Route('/client_show-{id}', name: 'app.client.show')]
    public function show(Request $request,Security $security, Client $clients,int $id)
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

                if ($clients->isDeleted ()== 1){
                    return $this->redirectToRoute('app.notfound');
                }

            }
            else{
                return $this->redirectToRoute('app.notfound');           
            }
        }

        $facture=$this->factureRepo->listeFacture($id);
        $factures=$this->factureRepo->gainFacture($id);

        $factureClientId=$this->factureRepo->factureClientId($id);
        $allFacture=$this->factureRepo->allFacture();
        /*$livraison=$this->livraisonRepo->nbreLivraison($id);
        $allLivraison=$this->livraisonRepo->allLivraison();*/
        if($allFacture==0){
            $pourcentageLivraison=0;
        }

        
        else{
            $pourcentageLivraison=100*$factureClientId/$allFacture;
        }
        $total=1;
            foreach ($facture as $item => $value){
                //dd(5);
                $reduction=$value->getTauxReduction();
                $tva=$value->getTotalTva();
                $ttc=$value->getTotalTtc();
                if($reduction>0){
                    $total=$total+$tva;
                }
                else{
                    $total=$total+$ttc;
                }
            } 
        //dd($total);
        return $this->render('compte-client.html.twig', [
            'clients'=>$clients,
            'facture'=>$facture,
            'factures'=>$factures,
            'total'=>$total,
            'pourcentageLivraison'=>$pourcentageLivraison
        ]);
    }
    #[Route('/facture_show-{id}', name: 'app.facture.show')]
    public function showFacture(ManagerRegistry $doctrine,Security $security,Request $request, Client $clients, int $id,PaginatorInterface $paginator)
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
        

        $client = $this->clientRepo->find($id);
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
        return $this->render('facture-client.html.twig', [
            'clients'=>$clients,
            'client'=>$client,
            'facture'=>$facture,
            'livraison'=>$livraison,
            'countFacture'=>$countFacture,
            'limit'=>$limit,
            'page'=>$page
        ]);
    }

    #[Route('/facture_grid-{id}', name: 'app.facture_grid.show')]
    public function factureGrid(ManagerRegistry $doctrine,Security $security,Request $request, Client $clients, int $id,PaginatorInterface $paginator)
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

        $client = $this->clientRepo->find($id);
        $countFacture = $this->factureRepo->countFacture($id);
        $findFacture = $this->factureRepo->findFacture($id);
        
       
        if ($findFacture==null) {
            $countEntete=1;
            $countEntete2=1;
        }
        else{
            foreach ($findFacture as $item => $value) {
                foreach ($value as $item1 => $value1) {
                
                $idFacture=$value1;     
                } 
            } 
            
            $countEntete = $this->enteteRepo->findEntete($idFacture);
            $countEntete2 = $this->enteteRepo->findEntete2($idFacture);
            
        }
        
        $limit = $countFacture+1;
        $page = $request->query->getInt('page', 1);
        $livraison = $this->livraisonRepo->findClient($id,$page,1);
        $facture = $doctrine->getRepository(Facture::class)->jointureTable($id,$page,$limit);

        //dd($facture);
        return $this->render('facture_grid.html.twig', [
            'clients'=>$clients,
            'client'=>$client,
            'facture'=>$facture,
            'livraison'=>$livraison,
            'countFacture'=>$countFacture,
            'countEntete'=>$countEntete,
            'countEntete2'=>$countEntete2,
            'limit'=>$limit,
            'page'=>$page
        ]);
    }

    #[Route('/facture_collecteLivraison', name: 'app.facture_collecteLivraison')]
    public function collectionLivraison(ManagerRegistry $doctrine,Security $security,Request $request,PaginatorInterface $paginator)
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

        //$client = $this->clientRepo->find($id);
        //$countFacture = $this->factureRepo->countFacture($id);
        $facture = $this->factureRepo->findAll();
        
       
       /* if ($findFacture==null) {
            $countEntete=1;
            $countEntete2=1;
        }
        else{
            foreach ($findFacture as $item => $value) {
                foreach ($value as $item1 => $value1) {
                
                $idFacture=$value1;     
                } 
            } 
            
            $countEntete = $this->enteteRepo->findEntete($idFacture);
            $countEntete2 = $this->enteteRepo->findEntete2($idFacture);*/
            
        
        
        //$limit = $countFacture+1;
        $page = $request->query->getInt('page', 1);
       // $livraison = $this->livraisonRepo->findClient($id,$page,1);
        //$facture = $doctrine->getRepository(Facture::class)->jointureTable($id,$page,$limit);

        //dd($facture);
        return $this->render('collection_livraison.html.twig', [
            //'clients'=>$clients,
            //'client'=>$client,
            'facture'=>$facture,
            //'livraison'=>$livraison,
            //'countFacture'=>$countFacture,
            //'countEntete'=>$countEntete,
            //'countEntete2'=>$countEntete2,
            //'limit'=>$limit,
            'page'=>$page
        ]);
    }

    #[Route('/supprimer_client-{id}', name: 'app.client.supprimer')]
    public function supprimer_client(Client $client, int $id): Response
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
        $client->setDeleted(true);
        $this->em->persist($client);
        $this->em->flush();
        return $this->redirectToRoute('app.client.liste');
       
    }

}
