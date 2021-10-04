<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list')]
    public function listAction(
        AuthorizationCheckerInterface $authorizationCheckerInterface
    ): Response
    {
        if (!$authorizationCheckerInterface->isGranted('ROLE_ADMIN')) {
            $this->addFlash('accessdenied', "Vous n'avez pas accès à cette page.");

            return $this->render('default/index.html.twig');
        }

        return $this->render(
            'user/list.html.twig',
            ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]
        );
    }

    #[Route('/users/create', name: 'user_create')]
    public function createAction(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        AuthorizationCheckerInterface $authorizationCheckerInterface
    ): RedirectResponse|Response
    {
        if (!$authorizationCheckerInterface->isGranted('ROLE_ADMIN')) {
            $this->addFlash('accessdenied', "Vous n'avez pas accès à cette page.");

            return $this->render('default/index.html.twig');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(
        User $user, Request $request,
        UserPasswordHasherInterface $passwordHasher,
        AuthorizationCheckerInterface $authorizationCheckerInterface
    ): RedirectResponse|Response
    {
        if (!$authorizationCheckerInterface->isGranted('ROLE_ADMIN')) {
            $this->addFlash('accessdenied', "Vous n'avez pas accès à cette page.");

            return $this->render('default/index.html.twig');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}

