<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserData
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createUser($user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $this->manager->persist($user);
        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            "L'utilisateur a bien été ajouté."
        );
    }

    public function editUser($user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $this->manager->flush();

        $session = new Session();
        $session->getFlashbag()->add(
            'success',
            "L'utilisateur a bien été modifié"
        );
    }
}
