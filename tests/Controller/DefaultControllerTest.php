<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private function getService($serviceName)
    {
        self::bootKernel();
        $service = self::$container
            ->get($serviceName);
        self::ensureKernelShutdown();
        return $service;
    }

    private function getTasks()
    {
        return $this
            ->getService('doctrine.orm.default_entity_manager')
            ->getRepository(Task::class)
            ->findAll()
        ;
    }

    private function login($username, $password)
    {
        return static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password,
        ]);
    }

    public function testTasksPage()
    {
        $client = $this->login('toto', 'toto');
        $client->request('GET', '/tasks');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testTaskPage()
    {
        $tasks = $this->getTasks();
        /**
         * @var Task $task
         */
        foreach ($tasks as $task) {
            $client = $this->login('toto', 'toto');
            $client->request('GET', '/tasks/'.$task->getId().'/edit');

            $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            self::ensureKernelShutdown();
        }
    }

    public function testHomepage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testHomepageWithLoggedUser()
    {
        $client = $this->login('toto', 'toto');
        $client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}