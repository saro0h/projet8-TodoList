<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\UserFormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository('App:User')->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @param UserFormHandler $formHandler
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function createAction(UserFormHandler $formHandler)
    {
        return $formHandler->handle(null, new User(), 'user/create.html.twig', []);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @param UserFormHandler $formHandler
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editAction(UserFormHandler $formHandler, User $user)
    {
        return $formHandler->handle(null, $user, 'user/edit.html.twig', ['user' => $user]);
    }
}
