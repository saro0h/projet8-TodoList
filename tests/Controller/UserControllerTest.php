<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HTTPFoundation\Response;
use App\Entity\User;

class UserControllerTest extends WebTestCase
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
        $user = $this->createUser('user', ['ROLE_USER', 'ROLE_ADMIN']);
        $this->client->loginUser($user);
        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->cleanDb();
    }

    public function testCreateAction()
    {
        $user = $this->createUser('user', ['ROLE_USER', 'ROLE_ADMIN']);
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[password][first]'] = 'secret';
        $form['user[password][second]'] = 'secret';
        $form['user[email]'] = 'user@example.com';
        $form['user[roles][0]']->tick();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        $this->cleanDb();
    }

    public function testEditAction()
    {
        $user = $this->createUser('user', ['ROLE_USER', 'ROLE_ADMIN']);
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'username_updated';
        $form['user[password][first]'] = 'secret';
        $form['user[password][second]'] = 'secret';
        $form['user[email]'] = 'user_updated@example.com';
        $this->client->submit($form);

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

    private function cleanDb(): void
    {
        $this->manager->getConnection()->query('DELETE FROM user');
    }
}
