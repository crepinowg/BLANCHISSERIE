<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Repository\LivraisonRepository;
use App\Repository\RappelRepository;
use App\Repository\FactureRepository;
use App\Repository\DepenseRepository;
use App\Repository\EmployeRepository;
use App\Repository\PaiementRepository;
use App\Repository\NotificationsRepository;
use App\Controller\FunctionImplementController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotifService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
class AccueilController extends AbstractController
{

     public function __construct(
        ClientRepository $clientRepo, 
        FactureRepository $factureRepo,
        EmployeRepository $employeRepo,
        LivraisonRepository $livraisonRepo ,
        RappelRepository $rappelRepo ,
        DepenseRepository $depenseRepo ,
        PaiementRepository $paiementRepo ,
        NotificationsRepository $notifRepo ,
        ContainerInterface $container,
        NotifService $notifService,
        FunctionImplementController $functionImplement,
        AuthorizationCheckerInterface $authorizationChecker,
        EntityManagerInterface $em){
        $this->em = $em;
        $this->clientRepo = $clientRepo;
        $this->livraisonRepo=$livraisonRepo;
        $this->rappelRepo=$rappelRepo;
        $this->depenseRepo=$depenseRepo;
        $this->factureRepo=$factureRepo;
        $this->employeRepo=$employeRepo;
        $this->paiementRepo=$paiementRepo;
        $this->notifRepo=$notifRepo;
        $this->notifService=$notifService;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
        $this->container =  $container->get('service_container');
    } 
    #[Route('/', name: 'app.accueil')]
    public function index(Security $security): Response
    {

        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            if($this->authorizationChecker->isGranted('ROLE_ADMIN') OR ($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
               
                $this->functionImplement->checking();

                $suspendu = $this->functionImplement->admin_suspendu();
                $statut = $this->functionImplement->gerant_suspendu();
                
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }
                if ($statut == 1){
                    return $this->redirectToRoute('app.logout');
                }
                if(!$this->getUser()->isIsVerify()){
                    return $this->redirectToRoute('app.logout');
                }
                
            }
            else{
                if($this->authorizationChecker->isGranted('ROLE_LAVAGE') OR ($this->authorizationChecker->isGranted('ROLE_LIVREUR'))){
                    $statutEmploye = $this->functionImplement->employe_suspendu();
                    if ($statutEmploye == 1){
                        return $this->redirectToRoute('app.logout');
                    }
                    elseif(!$this->getUser()->isIsVerify()){
                        return $this->redirectToRoute('app.logout');
                    }
                    else{
                        return $this->redirectToRoute('app.equipe.suivi');
                    }
                    
                      
                }
                else if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
                    return $this->redirectToRoute('app.cle.liste');  
                }
                else{
                    return $this->redirectToRoute('app.notfound'); 
                }
                          
            }
        }
       
        $aujourdhui = Date("Y-m-d");
        $programmeDeLaJournee=$this->rappelRepo->findAll();

        //dd($programmeDeLaJournee);

        $factureNonLivrer=$this->factureRepo->factureNonLivrer();
        
            $client = $this->clientRepo->findAll();
            $clientCount = $this->clientRepo->countClient();
            $factureLivrer=$this->factureRepo->factureLivrer();

            $clientCounts = $this->notifRepo->countClient();
            $countFactures = $this->notifRepo->countFactures();
            $countDepense = $this->notifRepo->countDepense();
            $countEmploye = $this->notifRepo->countEmploye();

            $twig = $this->container->get('twig');
            $twig->addGlobal('clientCounts', $clientCounts);
            $twig->addGlobal('countFactures', $countFactures);
            $twig->addGlobal('countDepense', $countDepense);
            $twig->addGlobal('countEmploye', $countEmploye);

            $factureAll=$this->factureRepo->findAll();
        
            $app = $this->getUser();
        
            /* Déclaration d'un objet d'instance. functionImplement */

            $dataTotalGeneral = $this->functionImplement->totalGeneral();
            
            
            $this->em->flush();
            return $this->render('home.html.twig', [
                'app'=>$app,
                'controller_name' => 'AccueilController',
                'clientCount'=>$clientCount,
                //'clientCounts'=>$clientCounts,
                'factureLivrer'=>$factureLivrer,
                'totalGeneral'=>$dataTotalGeneral["totalGeneral"],
                'factureAll'=>$factureAll,
                'nombreReduction'=>$dataTotalGeneral["nombreReduction"],
                'tvaGeneral'=>$dataTotalGeneral["tvaGeneral"],
                'ttcGeneral'=>$dataTotalGeneral["ttcGeneral"],
                'gainPerdu'=>$dataTotalGeneral["gainPerdu"],
                'totalDepenses'=>$dataTotalGeneral["totalDepenses"],
                'nombreRecuperation'=>$dataTotalGeneral["nombreRecuperation"],
                'factureNonLivrer'=>$factureNonLivrer,
                'programmeDeLaJournee'=>$programmeDeLaJournee,
                'aujourdhui'=>$aujourdhui
                //'factureCount'=>$factureCount,
                //'depenseCount'=>$depenseCount,
                //'employeCount'=>$employeCount,
            ]);
      
    }
    #[Route('/countNotifications', name: 'app.countNotifications')]
    public function countNotifications(Security $security): Response
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
             
        $clientCount = $this->clientRepo->countClient();
        $factureCount = $this->factureRepo->countFacture();
        $depenseCount = $this->depenseRepo->countDepense();
        $employeCount = $this->employeRepo->countEmploye();
        
        $this->em->flush();
        return $this->render('menu.html.twig', [
            'controller_name' => 'AccueilController',
            'clientCount'=>$clientCount,
            'factureCount'=>$factureCount,
            'depenseCount'=>$depenseCount,
            'employeCount'=>$employeCount,
            
        ]);
    }

    #[Route('/account', name: 'app.account')]
    public function account(Security $security): Response
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

        $app = $this->getUser();
        $_GLOBALS['app'] =  $this->getUser();
        
        $appAdmin = $this->getUser()->getAdministrateur();
        return $this->render('account.html.twig', [
            'app'=>$app,
            'appAdmin'=>$appAdmin,
            'controller_name' => 'SecurityController',
            
        ]);
    }
}
