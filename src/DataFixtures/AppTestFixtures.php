<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppTestFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        /**
         *   MAKE ANONYMOUS USER
         */
        $userAnonymous = new User();
        $userAnonymous->setUsername('anonyme')
            ->setEmail('anonymous@todo.fr')
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($userAnonymous, "password"));
        $manager->persist($userAnonymous);

        /**
         *   MAKE ADMIN USER
         */
        $userAdmin = new User();
        $userAdmin->setUsername('admin')
            ->setEmail('admin@todo.fr')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->hasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);

        /**
         *   MAKE SIMPLE USER
         */
        $userSimple = new User();
        $userSimple->setUsername('simple')
            ->setEmail('simple@todo.fr')
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($userSimple, "password"));
        $manager->persist($userSimple);

        /**
         *   MAKE EMPTY USER TASK
         */
        $task = new Task();
        $task->setTitle('empty_task')
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setAuthor(null);
        $manager->persist($task);

        /**
         *   MAKE ANONYMOUS TASK
         */
        $task = new Task();
        $task->setTitle('anomymous_task')
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setAuthor($userAnonymous);
        $manager->persist($task);

        /**
         *   MAKE ADMIN TASK
         */
        $task = new Task();
        $task->setTitle('admin_task')
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setAuthor($userAdmin);
        $manager->persist($task);

        /**
         *   MAKE SIMPLE TASK TODO
         */
        $task = new Task();
        $task->setTitle('simple_task_todo')
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setAuthor($userSimple);
        $manager->persist($task);

        /**
         *   MAKE SIMPLE TASK DONE
         */
        $task = new Task();
        $task->setTitle('simple_task_done')
            ->setContent($faker->text())
            ->setCreatedAt($faker->dateTime())
            ->setIsDone(true)
            ->setAuthor($userSimple);
        $manager->persist($task);


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
