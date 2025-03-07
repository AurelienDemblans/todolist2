<?php

namespace App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/accueil', name: 'homepage', methods: Request::METHOD_GET) ]
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
