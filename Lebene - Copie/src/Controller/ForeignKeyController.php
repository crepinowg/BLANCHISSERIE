<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForeignKeyController extends AbstractController
{
    #[Route('/foreign/key', name: 'app_foreign_key')]
    public function index(): Response
    {
        return $this->render('foreign_key/index.html.twig', [
            'controller_name' => 'ForeignKeyController',
        ]);
    }
}
