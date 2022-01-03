<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends BaseController
{
    public function testTasksPage()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testTaskPage()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageWithFakeData()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => '',
            'task[content]' => '',
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageWithGoodData()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => 'a new task',
            'task[content]' => 'a new content',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testEditTaskPageWithGoodData()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        $client->submitForm('Modifier', [
            'task[title]' => 'task updated',
            'task[content]' => 'content updated',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testToggleTask()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/toggle');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTask()
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskOtherUser()
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
