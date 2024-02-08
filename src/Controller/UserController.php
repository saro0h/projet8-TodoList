<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Security\UserVoter;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction(ManagerRegistry $doctrine)
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, new User());

        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = new User();

        $this->denyAccessUnlessGranted(UserVoter::ADD, $user);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            $doctrine->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
