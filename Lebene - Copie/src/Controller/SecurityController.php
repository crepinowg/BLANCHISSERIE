<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\CleProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTime; 

class SecurityController extends AbstractController
{

    
    public function __construct(
        CleProduitRepository $repoCle, 
     
        EntityManagerInterface $em,
       )
    {
        $this->repoCle= $repoCle;
        
        $this->em = $em;
    }


    #[Route('/security', name: 'app.security')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        //dd($this->getUser()->getGerant());
        $allCle = $this->repoCle->findAll();
        foreach ($allCle as $key => $value) {

            if($value->isStatut()==1 AND $value->getDureeReste()>0){
                $date = new DateTime($value->getCreatedAt());
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
      
        if($this->getUser()==null){
            return $this->render('pages-login.html.twig', [
                'controller_name' => 'SecurityController',
                'last_username'=>$lastUsername,
                'error'=>$error,
            ]);
        }

        else if($this->getUser()->getAdministrateur() != null){
            if($this->getUser()->getAdministrateur()->isSuspendu()==1){
                return $this->redirectToRoute('app.saisir.cle',['id'=>$this->getUser()->getAdministrateur()->getId()]);
            }
            else if($this->getUser()->getAdministrateur()->isSuspendu() == 0){
                //dd('OKI');
                return $this->render('pages-login.html.twig', [
                    'controller_name' => 'SecurityController',
                    'last_username'=>$lastUsername,
                    'error'=>$error,
                ]);
            }
        }

        else if($this->getUser()->getGerant() != null){
            //dd("1");
            if($this->getUser()->getGerant()->isStatut()==1){
               // dd("fghjkl");
                return $this->redirectToRoute('app.logout');
            }
            else if($this->getUser()->getGerant()->isStatut()==0){
               // dd('OKI');
                return $this->render('pages-login.html.twig', [
                    'controller_name' => 'SecurityController',
                    'last_username'=>$lastUsername,
                    'error'=>$error,
                ]);
            }
        }

        else if($this->getUser()->getEmploye() != null){
            if($this->getUser()->getEmploye()->isStatut()==1){
                return $this->redirectToRoute('app.logout');
            }
            else if($this->getUser()->getEmploye()->isStatut()==0){
                return $this->render('pages-login.html.twig', [
                    'controller_name' => 'SecurityController',
                    'last_username'=>$lastUsername,
                    'error'=>$error,
                ]);
            }
        }

       
       
        return $this->render('pages-login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username'=>$lastUsername,
            'error'=>$error,
        ]);
    }
    #[Route('/logout', name: 'app.logout')]
    public function logout(): void
    {
        // Cette méthode ne contient pas de code car Symfony gère automatiquement le processus de déconnexion.
        // La configuration appropriée doit être en place dans le fichier security.yaml.
        // Par défaut, Symfony redirigera l'utilisateur vers la page de login après la déconnexion.
        // Vous pouvez personnaliser ce comportement en configurant la redirection de déconnexion dans security.yaml.
       // return $this->redirectToRoute('app.secu');
    }

   
}
