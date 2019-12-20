<?php

namespace App\Controller\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @Route("/user/{id}/profile", name="user_profile")
     * 
     */

    public function profileAction(User $user)
    {
        //$user->$this->getUser();   

        return $this->render('user/profile.html.twig', ['user' => $user]);
        
    }
}