<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends BaseController
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

    public function testCreateTaskPageWithFakeData()
    {
        $client = $this->login('user', 'pass');
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => '',
            'task[content]' => ''
        ]);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageWithGoodData()
    {
        $client = $this->login('user', 'pass');
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => 'a new task',
            'task[content]' => 'a new content'
        ]);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testEditTaskPageWithGoodData()
    {
        $tasks = $this->getTasks();
        /** @var Task $task */
        $task = $tasks[0];
        $client = $this->login('user', 'pass');
        $client->request('GET', '/tasks/'.$task->getId().'/edit');

        $client->submitForm('Modifier', [
            'task[title]' => 'task updated',
            'task[content]' => 'content updated'
        ]);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testToggleTask()
    {
        $tasks = $this->getTasks();
        /** @var Task $task */
        $task = $tasks[0];
        $client = $this->login('user', 'pass');
        $client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTask()
    {
        $tasks = $this->getTasksForUser('user');
        /** @var Task $task */
        $task = $tasks[0];
        $client = $this->login('user', 'pass');
        $client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskOtherUser()
    {
        $tasks = $this->getTasksForUser('user');
        /** @var Task $task */
        $task = $tasks[0];
        $client = $this->login('admin', 'pass');
        $client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}