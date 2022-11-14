<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\UserDataInterface;

/**
 * Require ROLE_ADMIN for all the actions of this controller
 */
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    #[Route('/users', name: 'user_list')]
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll()
        ]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function createAction(
        Request $request,
        UserDataInterface $userData
    ): Response {
        $user = new User();
        $form = $this->createForm(
            UserType::class,
            $user
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData->createUser($user);

            $this->addFlash(
                'success',
                "L'utilisateur a bien été ajouté."
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(
        User $user,
        Request $request,
        UserDataInterface $userData
    ): Response {
        $form = $this->createForm(
            UserType::class,
            $user
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData->editUser($user);

            $this->addFlash(
                'success',
                "L'utilisateur a bien été modifié"
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/edit.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        );
    }
}
