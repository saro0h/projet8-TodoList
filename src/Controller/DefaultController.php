<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function indexAction(): Response
    {
        /*$session = $this->container->get('session');
        $securityToken = $session->get('_security_main');

        dump($session);
        dump($securityToken);*/

        return $this->render('default/index.html.twig');
    }
}
