<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HTTPFoundation\Response;
use App\Entity\User;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    private $manager;

    private $hasher;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->hasher = $this->client->getContainer()->get('security.user_password_hasher');
    }

    public function testLogin()
    {
        $user = $this->createUser('user');
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'secret';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('Se déconnecter', $crawler->filter('a.pull-right.btn.btn-danger')->text());

        $this->cleanDb();
    }

    public function testLoginBadUsername()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'secret';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());
    }

    public function testLoginBadPassword()
    {
        $user = $this->createUser('user');
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'secrete';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());

        $this->cleanDb();
    }

    private function createUser(string $username, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, 'secret'));
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
