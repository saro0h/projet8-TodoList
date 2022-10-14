<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        $roles = ["ROLE_USER", "ROLE_ADMIN"];

        for ($i = 0; $i < 4; $i++) {
            $switchRoles = ($i % 2) ? 0 : 1;
            $user = new User();
            if ($i == 0) {
                $user->setUsername('anonyme')
                    ->setEmail('anonymous@fatalbazooka.fr')
                    ->setRoles(["ROLE_USER"])
                    ->setPassword($this->hasher->hashPassword($user, "password"));
            } else {
                $user->setUsername($faker->name())
                    ->setEmail($faker->email())
                    ->setRoles([$roles[$switchRoles]])
                    ->setPassword($this->hasher->hashPassword($user, "password"));
            }
            $manager->persist($user);

            for ($t = 0; $t < 4; $t++) {
                $switchDoneStatus = ($t % 2) ? 0 : 1;
                $task = new Task();
                $task->setTitle($faker->sentence(2))
                    ->setContent($faker->text())
                    ->setIsDone($switchDoneStatus)
                    ->setAuthor($user);
                $manager->persist($task);
            }
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }
}
