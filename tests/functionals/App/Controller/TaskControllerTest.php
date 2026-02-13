<?php

declare(strict_types=1);

namespace Tests\functionals\App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class TaskControllerTest extends BaseWebTestCase
{
    public function testGetList()
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_list'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $crawler->filter('.create-task-btn')->text());
    }

    public function testCreateTask()
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Task test title';
        $form['task[content]'] = 'Task test content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('La tâche a été bien été ajoutée.', $crawler->filter('div.alert.alert-success')->text());
    }

    public function testMissingTitleFieldCreateTask()
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[content]'] = 'Task test content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testMissingContentFieldCreateTask()
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Task test title';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    public function testEditTask()
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Task test title']);
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('task_edit', ['id' => $task->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Task test edit title';
        $form['task[content]'] = 'Task test edit content';
        $client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('La tâche a bien été modifiée.', $crawler->filter('div.alert.alert-success')->text());
    }

    public function testToggleNotDoneTask()
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['isDone' => false]);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_toggle', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()), $crawler->filter('div.alert.alert-success')->text());
    }
    public function testToggleDoneTask()
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['isDone' => true]);
        $client->request(Request::METHOD_GET, $this->generateUrl('task_toggle', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString(sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()), $crawler->filter('div.alert.alert-success')->text());
    }

    public function testDeleteTask()
    {
        $client = $this->createAuthenticatedClient();
        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Task test edit title']);
        if(null === $task)
        {
            $task = $taskRepository->findOneBy(['title' => 'Task test title']);
        }
        $client->request(Request::METHOD_GET, $this->generateUrl('task_delete', ['id' => $task->getId()]));
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('La tâche a bien été supprimée.', $crawler->filter('div.alert.alert-success')->text());
    }
}
