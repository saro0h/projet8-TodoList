<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\Functional\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testTaskList(): void
    {
        $this->loginAs('user@user.com');
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //$this->assertStringContainsString('Welcome to Symfony', $crawler->filter('#container h1')->text());

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testCreateTaskAndVerifyUser()
    {
        $user = $this->loginAs('user@user.com');

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $title = 'Test of creation';

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]']->setValue($title);
        $form['task[content]']->setValue('test');
        $this->client->submit($form);

        $task = $this->taskRepository->findOneBy(['title' => $title]);

        $this->assertEquals($user->getId(), $task->getUser()->getId());
    }

    public function testEditTask()
    {
        $user = $this->loginAs('user@user.com');

        $task = $this->taskRepository->findOneBy(['user' => $user]);

        $crawler = $this->client->request('GET', "/tasks/" . $task->getId() . "/edit");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $add = ' (edited)';

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]']->setValue($task->getTitle() . $add);
        $form['task[content]']->setValue($task->getContent() . $add);
        $this->client->submit($form);

        $editedTask = $this->taskRepository->findOneBy(['id' => $task->getId()]);

        $this->assertNotEquals($task, $editedTask);
    }
}
