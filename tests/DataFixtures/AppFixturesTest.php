<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Task;
use App\Entity\User;

class AppFixturesTest extends KernelTestCase
{
    public function testLoad()
    {
        $kernel = self::bootKernel();
        $manager = $kernel->getContainer()->get('doctrine')->getManager();
        $manager->getConnection()->beginTransaction();

        $tasks = $manager->getRepository(Task::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        $this->assertEmpty($tasks);
        $this->assertEmpty($users);

        $hasher = new UserPasswordHasher($this->createMock(PasswordHasherFactoryInterface::class));
        $appFixtures = new AppFixtures($hasher);
        $appFixtures->load($manager);

        $tasks = $manager->getRepository(Task::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        $this->assertContainsOnly(Task::class, $tasks);
        $this->assertContainsOnly(User::class, $users);

        $manager->getConnection()->rollback();
    }
}
