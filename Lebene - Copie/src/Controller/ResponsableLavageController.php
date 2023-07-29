<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponsableLavageController extends AbstractController
{
    #[Route('/responsable/lavage', name: 'app_responsable_lavage')]
    public function index(): Response
    {
        return $this->render('responsable_lavage/index.html.twig', [
            'controller_name' => 'ResponsableLavageController',
        ]);
    }
}
