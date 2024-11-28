<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ZinzinController extends AbstractController
{
    #[Route('/zinzin', name: 'app_zinzin')]
    public function index(): Response
    {
        return $this->render('zinzin/index.html.twig', [
            'controller_name' => 'ZinzinController',
            'test' => 'bonjour'
        ]);
    }
}
