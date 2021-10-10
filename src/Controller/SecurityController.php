<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'bundles'         => $error,
        ));
    }

    /*#[Route('/login_check', name: 'login_check')]
    public function loginCheck()
    {
        // This code is not executed. Please uncomment it you want to apply a user email check
    }*/

    #[Route('/logout', name: 'logout')]
    public function logoutCheck()
    {
        return $this->render('default/index.html.twig');
    }
}

