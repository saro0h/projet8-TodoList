<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public const USER_ADMIN = "Audrey";
    public const USER_TEST_1 = "Morgane";
    public const USER_TEST_2 = "Clement";

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return array<string, User>
     */
    private function createUsers(ObjectManager $manager): array
    {
        // création de plusieurs users pour tester l'authentification et les fonctionnalités
        $users =
            [
                // donne le role ROLE_ADMIN (pour avoir accès aux infos user)
                'Jean' => (new User())->setUsername('Jean')->setEmail('jean@sf.com')->setRole('ROLE_ADMIN'),
                // donne le role ROLE_USER
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
            ];

        foreach ($users as $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }
        return $users;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // ajout de 3 users utilisés dans TaskFixtures pour les tests
        $userAdmin = new User;
        $userAdmin->setUsername('Audrey')
            ->setEmail('audrey@sf.com')
            ->setPassword($this->passwordHasher->hashPassword($userAdmin, 'passworD1!'))
            ->setRole('ROLE_ADMIN');
        $manager->persist($userAdmin);

        $userTest1 = new User;
        $userTest1->setUsername('Morgane')
            ->setEmail('morgane@sf.com')
            ->setPassword($this->passwordHasher->hashPassword($userTest1, 'passworD1!'));
        $manager->persist($userTest1);

        $userTest2 = new User;
        $userTest2->setUsername('Clement')
            ->setEmail('clement@sf.com')
            ->setPassword($this->passwordHasher->hashPassword($userTest2, 'passworD1!'));
        $manager->persist($userTest2);

        $this->createUsers($manager);
        $manager->flush();

        $this->addReference(self::USER_ADMIN, $userAdmin);
        $this->addReference(self::USER_TEST_1, $userTest1);
        $this->addReference(self::USER_TEST_2, $userTest2);
    }
}
