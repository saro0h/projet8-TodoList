<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@todolist.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'pass'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $this->addReference('admin', $admin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@todolist.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'pass'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $this->addReference('user', $user);

        $manager->flush();
    }
}
