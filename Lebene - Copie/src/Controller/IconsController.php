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

            // Récupérer le fichier téléchargé depuis la demande
        $file = $request->files->get('image');
        //dd($file);

        // Vérifier si un fichier a été téléchargé
        if ($file) {
            // Définir le répertoire de destination pour stocker les images
            $uploadDirectory = $this->getParameter('icones_uploads_directory');

             // Vérifier si le répertoire existe. Si non, le créer.
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true); // Créer le répertoire récursivement
            }

            // Générer un nom de fichier unique
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacer le fichier vers le répertoire de destination
            $file->move(
                $uploadDirectory,
                $fileName
            );

            // Vous pouvez ajouter ici la logique pour enregistrer le nom de fichier dans la base de données, si nécessaire
        }

        //dd("uiop");
            
        if (isset($nom)){
            $icons->setNomIcon($nom);
            $icons->setSyntaxeIcon($fileName);
            $this->em->persist($icons);
            $this->em->flush();
            return $this->redirectToRoute('app.icons');
        }

        return $this->render('ajout_icons.html.twig', [
            'controller_name' => 'IconsController',
        ]);
    }
}
