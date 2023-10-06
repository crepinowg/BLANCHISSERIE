<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rappel;
use Symfony\Component\Security\Core\Security;
use App\Controller\FunctionImplementController;
use App\Repository\RappelRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CalendrierController extends AbstractController
{

      /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        EntityManagerInterface $em, 
        FunctionImplementController $functionImplement,
        RappelRepository $rappelRepo,
        AuthorizationCheckerInterface $authorizationChecker,
        
        ){
       
        $this->em = $em;
        $this->functionImplement = $functionImplement;
        $this->rappelRepo = $rappelRepo;
        $this->authorizationChecker = $authorizationChecker;
    }


    #[Route('/calendrier', name: 'app.calendrier')]
    public function index(Security $security): Response
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

        return $this->render('calendrier.html.twig', [
            'controller_name' => 'CalendrierController',
        ]);
    }

    #[Route('/api/data', name: 'app.getData')]
    public function getData()
    {
        // Récupérer les données de la base de données
        $data = $this->rappelRepo->findAll();

        $tableau = array();
        
        foreach ($data as $key => $value) {
            $tableau[$key] = array(
                'id' => $value->getId(),
                'nom' => $value->getNom(),
                'description' => $value->getDescription(),
                'type' => $value->getTypeRappel(),
                'facture' => null,
                'jour' => $value->getJourAt(),
                'heureDebut' => $value->getHeureAt(),
                'jourFin' => $value->getDateFinAt(),
                'heureFin' => $value->getHeureFinAt(),
            );
           
            
            if ($value->getFacture() != null) {
                $tableau[$key]['facture'] = array(
                    "id" => $value->getFacture()->getId(),
                    "dateLivraison" => $value->getFacture()->getDateLivraison(),
                    "dateCollecte" => $value->getFacture()->getDateRecuperation(),
                    "factureIdNumber" => $value->getFacture()->getFactureIdNumber(),
                );
            }
            
            
        }
        
        //dd($tableau);
        // Convertir les données en format JSON
       // $jsonData = json_encode($tableau);

        // Retourner la réponse JSON
        return new JsonResponse($tableau);
    }

    #[Route('/ajout_rappel', name: 'app.rappel')]
    public function ajout_rappel(Request $request,Security $security): Response
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

        $rappel = new Rappel;

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }
            $nom=strtoupper($request->request->get('nom'));
            $description=strtolower($request->request->get('description'));
            
            $dateDebut=$request->request->get('dateRappel');
            $heureDebut=$request->request->get('heureDebut');
            $dateFin=$request->request->get('dateFin');
            $heureFin=$request->request->get('heureFin');
            $createdAt=Date('Y-m-d h:i');

            //dd("Nom:".$nom." Username:".$dateDebut." Mail:".$heureDebut." Username:".$dateFin." Mail:".$heureFin);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {
                if ($nom==null OR $dateDebut==null OR $heureDebut==null OR $dateFin==null OR $heureFin==null){
                    
                    $message="Veuillez remplir tout les champs avant soumission";            
                    return $this->json($message);

                }
                $dateDebutStr = strtotime($dateDebut); // convertir la dateDebut en dateDebut Unix
                $dateFinStr = strtotime($dateFin); // convertir la dateDebut en dateDebut Unix
                $heureDebutStr = strtotime($heureDebut); // convertir la dateDebut en dateDebut Unix
                $heureFinStr = strtotime($heureFin); // convertir la dateDebut en dateDebut Unix
                $aujourdhui =strtotime(Date('Y-m-d')); // dateDebut Unix de la dateDebut et heureDebut actuelles
                $heure = time();

                if ($dateDebutStr == $dateFinStr AND $dateDebutStr>=$aujourdhui AND $dateFinStr>=$aujourdhui ) {
                    if ($heureDebutStr < $heure OR  $heureFinStr<=$heure) {
                        $message="Les heures ne doivent pas être inférieur à l'heure actuelle";
                        return $this->json($message);
                    }
                    if ($heureDebutStr >= $heureFinStr) {
                        $message="L'heure de début ne doivent pas être inférieur ou supérieur a celle de fin et vice versa";
                        return $this->json($message);
                    }
                }

                if ($dateDebutStr < $aujourdhui OR $dateFinStr<$aujourdhui) {
                    $message="Veuillez revoir les dates de début et de fin";
                    return $this->json($message);
                }

                

                if ($dateDebutStr > $dateFinStr) {
            
                    $message="La date de début ne doivent pas être inférieur ou superieur a celle de fin et vice versa";
                    return $this->json($message);
                
                }

                

                

                

                if (isset($nom,$dateDebut,$heureDebut,$createdAt,$dateFin,$heureFin)){
                    $rappel->setNom($nom);
                    if($description!=null){
                        $rappel->setDescription($description);
                    }
                    $rappel->setJourAt($dateDebut);
                    $rappel->setHeureAt($heureDebut);
                    $rappel->setDateFinAt($dateFin);
                    $rappel->setHeureFinAt($heureFin);
                    $rappel->setCreatedAt($createdAt);
                    $rappel->setTypeRappel(1);

                    if($checkUser == false){
                        $rappel->setCreatedByGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $rappel->setCreatedByAdmin($admin);
                    }

                    $this->em->persist($rappel);
                    $this->em->flush();
                    
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.calendrier'),
                    ]);
                    
                }
            }
            else{
                return $this->render('ajout_rappel.html.twig', [
                    'controller_name' => 'CalendrierController',
                ]);
            }

        return $this->redirectToRoute('app.liste.gerant');
        
    }

    #[Route('/update_rappel-{id}', name: 'app.rappel.update')]
    public function update_rappel(Request $request, Security $security,Rappel $rappel, int $id): Response
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

        if(($this->authorizationChecker->isGranted('ROLE_GERANT_BLEU') OR $this->authorizationChecker->isGranted('ROLE_GERANT_NOIR'))){
            $checkUser=false;
            $gerantUser = $this->getUser()->getGerant();
        }

        else if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $checkUser=true;
            $admin = $this->getUser()->getAdministrateur();
        }
            $nom=strtoupper($request->request->get('nom'));
            $description=strtolower($request->request->get('description'));
            
            $dateDebut=$request->request->get('dateRappel');
            $heureDebut=$request->request->get('heureDebut');
            $dateFin=$request->request->get('dateFin');
            $heureFin=$request->request->get('heureFin');
            $createdAt=Date('Y-m-d h:i');

            //dd("Nom:".$nom." Username:".$dateDebut." Mail:".$heureDebut." Username:".$dateFin." Mail:".$heureFin);
            //dd((int)$niveau);
            if ($request->isXmlHttpRequest()) {
                if ($nom==null OR $dateDebut==null OR $heureDebut==null OR $dateFin==null OR $heureFin==null){
                    
                    $message="Nom:".$nom." dateDebut:".$dateDebut." heureDebut:".$heureDebut." dateFin:".$dateFin." heureFin:".$heureFin;            
                    return $this->json($message);

                }
                $dateDebutStr = strtotime($dateDebut); // convertir la dateDebut en dateDebut Unix
                $dateFinStr = strtotime($dateFin); // convertir la dateDebut en dateDebut Unix
                $heureDebutStr = strtotime($heureDebut); // convertir la dateDebut en dateDebut Unix
                $heureFinStr = strtotime($heureFin); // convertir la dateDebut en dateDebut Unix
                $aujourdhui =strtotime(Date('Y-m-d')); // dateDebut Unix de la dateDebut et heureDebut actuelles
                $heure = time();

                if ($dateDebutStr == $dateFinStr AND $dateDebutStr>=$aujourdhui AND $dateFinStr>=$aujourdhui ) {
                    if ($heureDebutStr < $heure OR  $heureFinStr<=$heure) {
                        $message="Les heures ne doivent pas être inférieur à l'heure actuelle";
                        return $this->json($message);
                    }
                    if ($heureDebutStr >= $heureFinStr) {
                        $message="L'heure de début ne doivent pas être inférieur ou supérieur a celle de fin et vice versa";
                        return $this->json($message);
                    }
                }

                if ($dateDebutStr < $aujourdhui OR $dateFinStr<$aujourdhui) {
                    $message=$aujourdhui."-".$dateDebutStr;
                    return $this->json($message);
                }

                

                if ($dateDebutStr > $dateFinStr) {
            
                    $message="La date de début ne doivent pas être inférieur ou superieur a celle de fin et vice versa";
                    return $this->json($message);
                
                }

                

                

                

                if (isset($nom,$dateDebut,$heureDebut,$createdAt,$dateFin,$heureFin)){
                    $rappel->setNom($nom);
                    if($description!=null){
                        $rappel->setDescription($description);
                    }
                    $rappel->setJourAt($dateDebut);
                    $rappel->setHeureAt($heureDebut);
                    $rappel->setDateFinAt($dateFin);
                    $rappel->setHeureFinAt($heureFin);
                    $rappel->setCreatedAt($createdAt);
                    $rappel->setTypeRappel(1);

                    if($checkUser == false){
                        $rappel->setCreatedByGerant($gerantUser);
                    }

                    else if($checkUser == true){
                        $rappel->setCreatedByAdmin($admin);
                    }

                    $this->em->persist($rappel);
                    $this->em->flush();
                    
                    return new JsonResponse([
                        'success' => true,
                        'redirect_url' => $this->generateUrl('app.calendrier'),
                    ]);
                    
                }
            }
            else{
                $rappel= $this->rappelRepo->find($id);
                return $this->render('update_rappel.html.twig', [
                    'controller_name' => 'CalendrierController',
                    "rappel"=>$rappel
                ]);
            }

        //return $this->redirectToRoute('app.liste.gerant');
        
    }
}
