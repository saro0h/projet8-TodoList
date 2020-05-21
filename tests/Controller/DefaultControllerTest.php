<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends BaseController
{

    public function testTasksPage()
    {
        $client = $this->login('user', 'pass');
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
            $client = $this->login('user', 'pass');
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
        $client = $this->login('user', 'pass');
        $client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}