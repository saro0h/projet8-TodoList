<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseController extends WebTestCase
{
    public const ADMIN_EMAIL = 'admin@todolist.com';
    public const USER_EMAIL = 'user@todolist.com';

    private function getService($serviceName)
    {
        //self::bootKernel();
        $service = self::getContainer()
            ->get($serviceName);
        //self::ensureKernelShutdown();
        return $service;
    }

    protected function getTasks()
    {
        return $this
            ->getService('doctrine.orm.default_entity_manager')
            ->getRepository(Task::class)
            ->findAll()
            ;
    }

    protected function getTasksForUser($username)
    {
        return $this
            ->getService('doctrine.orm.default_entity_manager')
            ->getRepository(Task::class)
            ->getTaskForUser($username)
            ;
    }

    protected function login($email)
    {
        $client = static::createClient();

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($email);

        $client->loginUser($testUser);

        return $client;
    }
}