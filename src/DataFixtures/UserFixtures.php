<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Fixtures NORMAL USER
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setEmail("user$i@email.com");
            $user->setRoles(array("ROLE_USER"));
            $plainPassword = "pass";
            $encoded = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
            $manager->persist($user);
        }

        // Fixtures ADMIN USER
        $admin = new User();
        $admin->setUsername("admin");
        $admin->setEmail("admin@email.com");
        $admin->setRoles(array("ROLE_ADMIN"));
        $plainPassword = "pass";
        $encoded = $this->userPasswordEncoder->encodePassword($admin, $plainPassword);
        $admin->setPassword($encoded);
        $manager->persist($admin);

        $manager->flush();
    }
}
