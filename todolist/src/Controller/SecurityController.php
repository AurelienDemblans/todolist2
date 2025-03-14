<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: Request::METHOD_GET) ]
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    #[Route('/login_check', name: 'login_check', methods: Request::METHOD_GET) ]
    public function loginCheck()
    {
        // This code is never executed.
    }

    #[Route('/logout', name: 'logout', methods: Request::METHOD_GET) ]
    public function logoutCheck()
    {
        // This code is never executed.
    }
}
