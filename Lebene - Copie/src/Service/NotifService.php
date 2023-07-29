<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ClientRepository;
use App\Repository\LivraisonRepository;
use App\Repository\FactureRepository;
use App\Repository\DepenseRepository;
use App\Repository\EmployeRepository;
use App\Repository\NotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NotifService
{

    public function __construct(
        ClientRepository $clientRepo, 
        FactureRepository $factureRepo,
        EmployeRepository $employeRepo,
        LivraisonRepository $livraisonRepo ,
        DepenseRepository $depenseRepo ,
        NotificationsRepository $notifRepo ,
        ContainerInterface $container,
        EntityManagerInterface $em){
        $this->em = $em;
        $this->clientRepo = $clientRepo;
        $this->livraisonRepo=$livraisonRepo;
        $this->depenseRepo=$depenseRepo;
        $this->factureRepo=$factureRepo;
        $this->employeRepo=$employeRepo;
        $this->notifRepo=$notifRepo;
        $this->container =  $container->get('service_container');
    }
    public function notifsFonction()
    {
        $clientCounts = $this->notifRepo->countClient();
        $countFactures = $this->notifRepo->countFactures();
        $countDepense = $this->notifRepo->countDepense();
        $countEmploye = $this->notifRepo->countEmploye();

        $twig = $this->container->get('twig');
        $twig->addGlobal('clientCounts', $clientCounts);
        $twig->addGlobal('countFactures', $countFactures);
        $twig->addGlobal('countDepense', $countDepense);
        $twig->addGlobal('countEmploye', $countEmploye);
    }
}