<?php

namespace App\Controller;

use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IncrementController extends AbstractController
{
    #[Route('/increment', name: 'app_increment')]
    public function index(): Response
    {
        return $this->render('increment/index.html.twig', [
            'controller_name' => 'IncrementController',
        ]);
    }
}
