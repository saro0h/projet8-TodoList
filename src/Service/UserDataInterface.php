<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

interface UserDataInterface
{
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher
    );
    public function createUser(User $user): void;
    public function editUser(User $user): void;
    public function deleteUser(User $user): void;
}
