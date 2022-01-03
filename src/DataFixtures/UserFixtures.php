<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@todolist.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'pass'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $this->addReference('admin', $admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@todolist.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'pass'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $this->addReference('user', $user);

        $manager->flush();
    }
}
