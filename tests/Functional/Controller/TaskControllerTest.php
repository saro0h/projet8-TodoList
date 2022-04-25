<?php

namespace App\Tests\Functional\Controller;

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
        $this->testEntityList('user@user.com', 'task_list');
    }

//    public function testTaskList(): void
//    {
//        $this->loginAs('user@user.com');
//        $crawler = $this->client->request('GET', '/tasks');
//
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//        //$this->assertStringContainsString('Welcome to Symfony', $crawler->filter('#container h1')->text());
//
//        $this->assertResponseIsSuccessful();
//        //$this->assertSelectorTextContains('h1', 'Hello World');
//    }

    public function testCreateTaskAndVerifyUserAndDelete()
    {
        $user = $this->loginAs('user@user.com');

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $title = 'Test of creation ' . rand(100, 200);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]']->setValue($title);
        $form['task[content]']->setValue('test');
        $this->client->submit($form);

        $task = $this->taskRepository->findOneBy(['title' => $title]);
        $this->assertEquals($user->getId(), $task->getUser()->getId());

        $crawler = $this->client->request('GET', "/tasks/" . $task->getId() . "/delete");

        $task = $this->taskRepository->findOneBy(['title' => $title]);
        $this->assertEquals(false, (bool)$task);
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

//    public function testToggleTask()
//    {
//        $user = $this->loginAs('user@user.com');
//
//        $task = $this->taskRepository->findOneBy(['isDone' => false]);
//        $this->assertEquals(true, (bool)$task);
//
//        $crawler = $this->client->request('GET', "/tasks/" . $task->getId() . "/toggle");
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//        $editedTask = $this->taskRepository->findOneBy(['id' => $task->getId()]);
//        $this->assertEquals(true, $editedTask->isDone());
//    }

}
