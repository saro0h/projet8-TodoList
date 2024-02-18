<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HTTPFoundation\Response;
use App\Entity\User;
use App\Entity\Task;

class TaskControllerTest extends WebTestCase
{
    private $client;

    private $manager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testListAction()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateAction()
    {
        $user = $this->createUser('user');
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Titre';
        $form['task[content]'] = 'Contenu';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        $this->cleanDb();
    }

    public function testEditAction()
    {
        $user = $this->createUser('user');
        $taskId = $this->createTask($user);

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/tasks/'.$taskId.'/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Titre modifié';
        $form['task[content]'] = 'Contenu modifié';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        $this->cleanDb();
    }

    public function testToggleTaskAction()
    {
        $user = $this->createUser('user');
        $taskId = $this->createTask($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/'.$taskId.'/toggle');

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        $this->cleanDb();
    }

    public function testDeleteTaskAction()
    {
        $user = $this->createUser('user');
        $taskId = $this->createTask($user);

        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/'.$taskId.'/delete');

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        $this->cleanDb();
    }

    private function createUser(string $username, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword('secret');
        $user->setEmail('test@example.com');

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

    private function createTask(User $user): int
    {
        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Titre');
        $task->setContent('Contenu');

        $this->manager->persist($task);
        $this->manager->flush();

        return $task->getId();
    }

    private function cleanDb(): void
    {
        $this->manager->getConnection()->query('DELETE FROM task');
        $this->manager->getConnection()->query('DELETE FROM user');
    }
}
