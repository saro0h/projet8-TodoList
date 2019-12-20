<?php

namespace App\Controller\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class UserDeleteController extends AbstractController
{

    /**
     * @Route("/users/{id}/delete", name="user_delete")
     */
    public function deleteAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas !');
        }
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($user);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}