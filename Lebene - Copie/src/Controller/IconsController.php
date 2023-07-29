<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Icons;
use App\Entity\Paiement;
use Symfony\Component\Security\Core\Security;

use App\Controller\FunctionImplementController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;

class IconsController extends AbstractController
{

    public function __construct(
       
        EntityManagerInterface $em,
        FunctionImplementController $functionImplement,
        AuthorizationCheckerInterface $authorizationChecker,
    ){
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->functionImplement = $functionImplement;
 
    }
    #[Route('/icons', name: 'app.icons')]
    public function index(Request $request,Security $security): Response
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

        $icons =  new Icons;

        $nom = $request->request->get('nom');
        $syntaxe = $request->request->get('syntaxe');
        /*$img=$_FILES["syntaxe"]["name"];
        $info_img=@getImageSize($img);
        dd($info_img);*/
        
        if (isset($nom)){
            $icons->setNomIcon($nom);
            $icons->setSyntaxeIcon($syntaxe);
            $this->em->persist($icons);
            $this->em->flush();
            return $this->redirectToRoute('app.icons');
           /* /** @var UploadedFile $file 
            $file = $form->get('fileField')->getData();
            
           
            $destination = 'chemin/vers/votre/dossier';
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($destination, $fileName);*/
            
            // Faites ce que vous voulez avec le fichier téléchargé (par exemple, enregistrez le nom du fichier dans la base de données, etc.)
            
            // Redirigez ou affichez une réponse appropriée
        }

        return $this->render('ajout_icons.html.twig', [
            'controller_name' => 'IconsController',
        ]);
    }
}
