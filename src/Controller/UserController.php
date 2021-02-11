<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('user/list.html.twig', [
                'users' => $this->getDoctrine()->getRepository(User::class)->findAll()
            ]);
        } else {
            return $this->render('error/error.html.twig');
        }
    }

    /**
     * @Route("/users/create", name="user_create")
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $userPasswordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit", requirements={"id" = "[a-z0-9\-]*"})
     */
    public function editAction(UserRepository $userRepository, $id, Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $user = $userRepository->find($id);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            //$password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            //$user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(), 'user' => $user
            ]);
        } else {
            return $this->render('error/error.html.twig');
        }
    }
}
