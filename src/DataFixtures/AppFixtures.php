<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User;
        $user->setEmail('user@test.fr');
        $user->setUsername('user');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        $testUser = new User;
        $testUser->setEmail('test@test.fr');
        $testUser->setUsername('test');
        $test_password = $this->hasher->hashPassword($testUser, 'test');
        $testUser->setPassword($test_password);
        $manager->persist($testUser);

        $task = new Task;
        $task->setTitle('Test task');
        $task->setContent('Test content');
        $manager->persist($task);

        $manager->flush();
    }
}
