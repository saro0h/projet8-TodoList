<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends BaseController
{
    public function testTasksPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks');

        self::assertResponseIsSuccessful();
    }

    public function testFinishedPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/finished');

        self::assertResponseIsSuccessful();
    }

    public function testShowPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/show');

        self::assertResponseIsSuccessful();
    }

    public function testTaskPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        self::assertResponseIsSuccessful();
    }

    public function testCreateTaskPageWithFakeData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => '',
            'task[content]' => '',
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testCreateTaskPageWithGoodData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        $client->submitForm('Ajouter', [
            'task[title]' => 'a new task',
            'task[content]' => 'a new content',
            'task[category]' => 1,
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testEditTaskPageWithGoodData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        $client->submitForm('Modifier', [
            'task[title]' => 'task updated',
            'task[content]' => 'content updated',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testToggleTask(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/toggle');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTask(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskOtherUser(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
