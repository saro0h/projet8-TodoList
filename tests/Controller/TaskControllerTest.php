<?php

namespace Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{

    protected $em;
    protected $client;
    
    const TASKS_URL = '/tasks';
    const TASKS_BASE_URL = '/tasks/';
    const TASK_CREATE_URL = '/task/create';
    const TASK_DELETE_SUFFIX_URL = '/delete';
    const TASK_EDIT_SUFFIX_URL = '/edit';
    const TASK_TOGGLE_SUFFIX_URL = '/toggle';
    

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::$container->get(EntityManagerInterface::class);
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * After each test, a rollback reset the state of
     * the database
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }
    }

    /** Test User list page */
    public function testList()
    {
        $rep = $this->em->getRepository(User::class);

        /* No user connected */
        $this->client->request('GET', self::TASKS_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $rep = $this->em->getRepository(User::class);

        /* No user connected */
        $this->client->request('GET', self::TASK_CREATE_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASK_CREATE_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', self::TASK_CREATE_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* Task form */
        $this->assertStringContainsString('<input type="text" id="task_title"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<textarea id="task_content" name="task[content]"', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();
        $this->client->submit($form);
        $this->assertStringContainsString('div class="form-group has-error"', $this->client->getResponse()->getContent());

        // set some values
        $form['task[title]'] = 'Task title';
        $form['task[content]'] = 'Text content';

        // submit the form
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Task title', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Text content', $this->client->getResponse()->getContent());
    }

    public function testEdit()
    {
        $rep = $this->em->getRepository(User::class);
        $taskRep = $this->em->getRepository(Task::class);
        $testTask = $taskRep->findOneBy([]);
        $id = $testTask->getId();

        /* No user connected */
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_EDIT_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_EDIT_SUFFIX_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_EDIT_SUFFIX_URL);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* Task form */
        $this->assertStringContainsString('<input type="text" id="task_title"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('<textarea id="task_content" name="task[content]"', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Modifier')->form();

        // set some values
        $form['task[title]'] = 'Task new title';
        $form['task[content]'] = 'Text new content';

        // submit the form
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Task new title', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Text new content', $this->client->getResponse()->getContent());
    }

    public function testToggleTask()
    {
        $rep = $this->em->getRepository(User::class);
        $taskRep = $this->em->getRepository(Task::class);
        $testTask = $taskRep->findOneBy([]);
        $id = $testTask->getId();

        /* No user connected */
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_TOGGLE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* Normal user connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_TOGGLE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertStringContainsString('a bien été marquée comme faite', $this->client->getResponse()->getContent());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_TOGGLE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertStringContainsString('a bien été marquée comme faite', $this->client->getResponse()->getContent());
    }

    public function testDeleteTask()
    {
        $rep = $this->em->getRepository(User::class);
        $taskRep = $this->em->getRepository(Task::class);
        $testTask = $taskRep->findOneBy([]);
        $id = $testTask->getId();

        /* No user connected */
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_DELETE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        /* With Admin Role */
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_DELETE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertStringContainsString('Tâche non supprimée, une tâche ne peut etre supprimée que par son auteur.', $this->client->getResponse()->getContent());

        /* Task owner connected */
        /** @var User $testUser */
        $testUser = $rep->findOneBy(['username' => 'Evohe']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $id . self::TASK_DELETE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertStringContainsString('La tâche a bien été supprimée.', $this->client->getResponse()->getContent());


        /* Admin delete anonymous task */
        $testUser = $rep->findOneBy(['username' => 'anonymous']);
        $task = new Task();
        $task->setTitle('Title Anon');
        $task->setContent('Content');
        $testUser->addTask($task);
        $this->em->persist($task);
        $this->em->persist($testUser);
        $this->em->flush();

        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', self::TASKS_BASE_URL . $task->getId() . self::TASK_DELETE_SUFFIX_URL);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertStringContainsString('La tâche a bien été supprimée.', $this->client->getResponse()->getContent());
    }
}
