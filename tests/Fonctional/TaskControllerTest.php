<?php

namespace App\Tests\Fonctional;

use App\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{

    private $client;

    /** @var EntityManager */
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = DefaultControllerTest::createAuthenticationClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function getTask()
    {
        $query = $this->entityManager->createQuery(
            'SELECT t
            FROM App\Entity\Task t'
        )->setMaxResults(1);
        return $query->getOneOrNullResult();
    }

    public function testListAction()
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#tasks_list'));
    }

    public function testCreateAction()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $formValues = [
            'task[title]' => time(),
            'task[content]' => time(),
        ];

        $this->client->submit($form, $formValues);

        $this->assertTrue($this->client->getResponse()->isRedirect('/tasks'));

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe !', $crawler->filter('body')->text());

        $repository = $this->entityManager->getRepository(Task::class);
        $newTask = $repository->findOneBy([
            'title' => $formValues['task[title]'],
            'content' => $formValues['task[content]']
        ]);

        $this->taskId = $newTask->getId();

        $this->assertNotNull($newTask, 'La tâche n\'est pas ajoutée');
    }

    public function testEditAction()
    {
        $task = $this->getTask();
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();

        $formValues = [
            'task[title]' => 'test title edit',
            'task[content]' => 'test content edit',
        ];

        $this->client->submit($form, $formValues);

        $this->assertTrue($this->client->getResponse()->isRedirect('/tasks'));

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe !', $crawler->filter('body')->text());

        $repository = $this->entityManager->getRepository(Task::class);
        $editTask = $repository->findOneBy([
            'title' => $formValues['task[title]'],
            'content' => $formValues['task[content]']
        ]);

        $this->assertNotNull($editTask, 'La tâche n\'est pas modifiée');
    }

    public function testToggleTaskAction()
    {
        $repository = $this->entityManager->getRepository(Task::class);

        $lastTask = $this->getTask();

        $lastState = $lastTask->isDone();

        $this->client->request('GET', '/tasks/'.$lastTask->getId().'/toggle');

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe !', $crawler->filter('body')->text());

        $currentTask = $repository->find($lastTask->getId());

        $this->assertNotEquals($lastState, $currentTask->isDone());
    }

    public function testDeleteTaskAction()
    {
        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository(Task::class);
        $task = $this->getTask();

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('Superbe !', $crawler->filter('body')->text());

        $this->assertNull($repository->findOneBy(['id' => $task->getId()]));
    }
}
