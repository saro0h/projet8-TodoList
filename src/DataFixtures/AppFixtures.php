<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;



    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
           
        $useradmin = new User();
        $useradmin->setUsername('useradmin');
        $useradmin->setEmail('useradmin@symfony.com');
        $useradmin->setPassword($this->encoder->encodePassword($useradmin, 'computer'));
        $useradmin->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($useradmin);
            
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@symfony.com');
        $user->setPassword($this->encoder->encodePassword($user, 'computer'));
        $user->setRoles(['ROLE_USER']);
        
        $manager->persist($user);

        $users =  [$useradmin, $user] ;
        foreach ($users as $user) {
            for ($i = 1; $i <= mt_rand(4,6); $i++) {
                $task = new Task();

                $task->setTitle($faker->sentence());
                $task->setContent($faker->paragraph());
                $task->setUser($user);

                $manager->persist($task);
            }
        }
        
        $manager->flush();
    }
}