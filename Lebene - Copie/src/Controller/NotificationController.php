<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NotificationsRepository;
use App\Entity\Notifications;
use Symfony\Component\Security\Core\Security;

use Doctrine\ORM\EntityManagerInterface;
use App\Controller\FunctionImplementController;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends AbstractController
{

    public function __construct(
        NotificationsRepository $notifRepo , 
        FunctionImplementController $functionImplement,
        EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->notifRepo = $notifRepo;
        $this->functionImplement = $functionImplement;
        
    } 
    #[Route('/notification', name: 'app.notification')]
    public function index(Security $security): Response

    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            return $this->redirectToRoute('app.security');
            
        }
        else{
            
                $suspendu = $this->functionImplement->admin_suspendu();
                if ($suspendu == 1){
                    return $this->redirectToRoute('app.security');
                }

        }

        $notifications=$this->notifRepo->findAllOrder();
        return $this->render('historique.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications'=>$notifications
        ]);
    }

    #[Route('/markView-{id}', name: 'app.markView')]
    public function markView(Request $request, Security $security,Notifications $notifications, int $id): Response

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

        $notifications=$this->notifRepo->findMarkView($id);

       foreach ($notifications as $key => $value) {
        if($value->isReader() == 0){
            $value->setReader(1);
            $this->em->persist($value);
        }
        elseif($value->isReader() == 1){
            $value->setReader(0);
            $this->em->persist($value);
        }
       }
       $this->em->flush();
       return $this->redirectToRoute('app.notification');
    }
}
