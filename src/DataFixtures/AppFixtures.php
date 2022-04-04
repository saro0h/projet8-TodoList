<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {

        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('admin@admin.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user, "admin"))
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user, "user"))
            ->setUsername("user")
            ->setRoles([]);
        $manager->persist($user);

        $manager->flush();
    }
}
