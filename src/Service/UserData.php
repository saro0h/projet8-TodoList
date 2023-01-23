<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserData implements UserDataInterface
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * @param User $user
     * @return void
     */
    public function createUser(User $user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function editUser(User $user): void
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $this->manager->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}
