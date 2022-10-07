<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        $roles = ["ROLE_USER", "ROLE_ADMIN"];

        for ($i = 0; $i < 5; $i++) {
            $random = random_int(0, 1);
            $user = new User();
            if ($i == 0) {
                $user->setUsername('anonyme')
                    ->setEmail('anonymous@fatalbazooka.fr')
                    ->setRoles(["ROLE_USER"])
                    ->setPassword($this->hasher->hashPassword($user, "password"));
            } else {
                $user->setUsername($faker->name())
                    ->setEmail($faker->email())
                    ->setRoles([$roles[$random]])
                    ->setPassword($this->hasher->hashPassword($user, "password"));
            }
            $manager->persist($user);

            for ($t = 0; $t < 5; $t++) {
                $task = new Task();
                $task->setTitle($faker->sentence(2))
                    ->setContent($faker->text())
                    ->setCreatedAt($faker->dateTime())
                    ->setIsDone(random_int(0, 1))
                    ->setAuthor($user);
                $manager->persist($task);
            }
        }



        $manager->flush();
    }
}
