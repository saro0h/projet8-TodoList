<?php

namespace App\DataFixtures;

use App\Entity\Task;
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

        $users = [];

        /* users */
        $user = new User();
        $user->setEmail('admin@admin.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user, "admin"))
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN']);
        $users[] = $user;
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user, "user"))
            ->setUsername("user")
            ->setRoles([]);
        $users[] = $user;
        $manager->persist($user);

        /* tasks */
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $task = (new Task())
                    ->setUser($user)
                    ->setTitle($faker->sentence(4))
                    ->setContent($faker->sentence(7));
                $manager->persist($task);
            }
        }

        /* anonymous tasks */
        for ($i = 0; $i < 3; $i++) {
            $task = (new Task())
                ->setTitle($faker->sentence(4))
                ->setContent($faker->sentence(10));
            $manager->persist($task);
        }

        $manager->flush();
    }
}
