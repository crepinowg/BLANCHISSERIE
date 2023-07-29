<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GerantRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Security;

use App\Controller\FunctionImplementController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Gerant;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class GerantController extends AbstractController
{

    public function __construct(
        GerantRepository $gerantRepo ,
        UtilisateurRepository $utilisateurRepo , 
        AuthorizationCheckerInterface $authorizationChecker,
        FunctionImplementController $functionImplement,
        EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->gerantRepo = $gerantRepo;
        $this->functionImplement = $functionImplement;
        $this->authorizationChecker = $authorizationChecker;
        $this->utilisateurRepo = $utilisateurRepo;
    }

    #[Route("/dataGerant", name: 'app.data.gerant')]
    public function sendDataGerant()
    {

        $gerant = $this->utilisateurRepo->findDataGerant();
       
        $tableauGerant = array();

        foreach($gerant as $key => $value) {

            $tableauGerant[$key] = array(
                'id'=>$value->getId(),
                'email' => $value->getEmail(),
                'numero' => $value->getNumero(),
                'username' => $value->getUsername(),
            );
        }

        return new JsonResponse($tableauGerant);
    }

    #[Route('/gerant_liste', name: 'app.liste.gerant')]
    public function listeGerant(Security $security): Response
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

        $gerant = $this->gerantRepo->findAll();

        $this->addFlash('NoGerant','Aucun gérant enregistrer. Ajouter  pour commencer');
        return $this->render('liste_gerant.html.twig', [
            'gerant'=>$gerant,
            
        ]);

    }

    #[Route('/gerant_show-{id}', name: 'app.gerant.show')]
    public function show(Request $request, Gerant $gerant,int $id)
    {
        //$gerant=$this->gerantRepo->listeGerant($id);
        //dd($gerant);
        return $this->render('compte_gerant.html.twig', [
            'gerant'=>$gerant,
            
        ]);
    }

    #[Route('suspendre_gerant-{id}', name: 'app.gerant.suspendre')]
    public function suspendre_gerant(Request $request, Gerant $gerant, int $id): Response
    {
        if($gerant->isStatut()==0){
            $gerant->setStatut(true);
        }
        else if($gerant->isStatut()==1){
            $gerant->setStatut(false);
        }
        $this->em->persist($gerant);
        $this->em->flush();
        return $this->redirectToRoute('app.liste.gerant');
    }
}

?>
