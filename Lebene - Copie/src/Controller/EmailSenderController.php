<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Entity\Client;
use App\Entity\Livraison;
use App\Entity\Entete;
use App\Entity\Facture;
use App\Entity\Administrateur;
use App\Entity\FactureEquipe;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class EmailSenderController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $em, 
        FactureEquipeRepository $factureEquipeRepo,
        RappelRepository $rappelRepo,
        ClientRepository $clientRepo,
        EmployeEquipeRepository $employeEquipeRepo, 
        FactureRepository $factureRepo,
        TarifsRepository $tarifsRepo,
        EnteteRepository $enteteRepo,
        LivraisonRepository $livraisonRepo,
        EntrepriseRepository $entrepriseRepo
        ){
        $this->clientRepo = $clientRepo;
        $this->employeEquipeRepo = $employeEquipeRepo;
        $this->rappelRepo = $rappelRepo;
        $this->factureEquipeRepo = $factureEquipeRepo;
        $this->factureRepo = $factureRepo;
        $this->tarifsRepo = $tarifsRepo;
        $this->enteteRepo = $enteteRepo;
        $this->livraisonRepo = $livraisonRepo;
        $this->entrepriseRepo = $entrepriseRepo;
        $this->em = $em;
    }

    
    #[Route('/emailFacture-{id}', name: 'app_email_facture')]
    public function emailFacture(Client $clients, int $id, Request $request,ManagerRegistry $doctrine,PaginatorInterface $paginator): Response
    {
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
        return $this->render('facture_sender.html.twig', [
            'controller_name' => 'EmailSenderController',
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

}
