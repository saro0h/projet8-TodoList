<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Task;
use App\Entity\User;

class AppFixtures extends Fixture
{
    /**
     * @var $hasher password hasher
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Load data in database
     * @param ObjectManager $manager object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];

        for ($i = 0; $i <= 5; $i++) {
            $user = new User();
            $user->setPassword($this->hasher->hashPassword($user, 'secret'));
            $user->setRoles(['ROLE_USER']);

            if ($i === 0) {
                $user->setUsername('anonyme');
                $user->setEmail('anonyme@gmail.com');
            } else if ($i === 1) {
                $user->setUsername('simon');
                $user->setEmail('simoncharbonnier@gmail.com');
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            } else {
                $user->setUsername($faker->username());
                $user->setEmail($faker->safeEmail());
            }

            $users[] = $user;
            $manager->persist($user);
        }

        $taskTitles = [
            'Passer l\'aspirateur',
            'Faire la vaisselle',
            'Ranger ma chambre',
            'Aller faire des courses',
            'Promener le chien',
            'Etendre le linge',
            'Faire du sport',
            'Méditer',
            'Lire mon livre',
            'Arroser les plantes'
        ];

        for ($i = 0; $i <= 9; $i++) {
            $task = new Task();

            if ($i < 2) {
                $task->setUser($users[0]);
            } else {
                $task->setUser($users[array_rand($users)]);
            }

            $task->setTitle($taskTitles[$i]);
            $task->setContent($faker->paragraph());
            $task->setCreatedAt($faker->dateTime());

            $manager->persist($task);
        }

        $manager->flush();
    }
}
