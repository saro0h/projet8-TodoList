<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseController extends WebTestCase
{
    private function getService($serviceName)
    {
        self::bootKernel();
        $service = self::$container
            ->get($serviceName);
        self::ensureKernelShutdown();
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

    protected function login($username, $password)
    {
        return static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password,
        ]);
    }
}