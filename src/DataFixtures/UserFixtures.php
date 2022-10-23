<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public const ANONYME_USER_REFERENCE = "anonymeUser";

    private function createUsers(ObjectManager $manager): array
    {
        // création de plusieurs users pour tester l'authentification et les fonctionnalités
        $users =
            [
                'Jean' => (new User())->setUsername('Jean')->setEmail('jean@sf.com'),
                'Loic' => (new User())->setUsername('Loic')->setEmail('loic@sf.com'),
                'Antoine' => (new User())->setUsername('Antoine')->setEmail('antoine@sf.com'),
                'Claire' => (new User())->setUsername('Claire')->setEmail('claire@sf.com'),
                'Franck' => (new User())->setUsername('Franck')->setEmail('franck@sf.com'),
                'Thomas' => (new User())->setUsername('Thomas')->setEmail('thomas@sf.com'),
                'Muriel' => (new User())->setUsername('Muriel')->setEmail('muriel@sf.com'),
                'Floriane' => (new User())->setUsername('Floriane')->setEmail('floriane@sf.com'),
                'Marie' => (new User())->setUsername('Marie')->setEmail('marie@sf.com'),
                'Marine' => (new User())->setUsername('Marine')->setEmail('marine@sf.com'),
                'John' => (new User())->setUsername('John')->setEmail('john@sf.com'),
                'Audrey' => (new User())->setUsername('Audrey')->setEmail('audrey@sf.com'),
                'Morgane' => (new User())->setUsername('Morgane')->setEmail('morgane@sf.com'),
                'Clément' => (new User())->setUsername('Clément')->setEmail('clement@sf.com')
            ];

        foreach ($users as $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }
        return $users;
    }

    public function load(ObjectManager $manager): void
    {
        // auteur anonyme pour les tâche déjà existantes
        $anonymeUser = new User;
        $anonymeUser->setUsername('anonyme')
            ->setEmail('anonyme@sf.com')
            ->setPassword($this->passwordHasher->hashPassword($anonymeUser, 'passworD1!'));
        $manager->persist($anonymeUser);

        $users = $this->createUsers($manager);
        $manager->flush();
    }
}
