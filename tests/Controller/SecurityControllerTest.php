<?php

namespace Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    protected $em;
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->em = static::$container->get(EntityManagerInterface::class);
    }



    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /* User form */
        $this->assertStringContainsString('name="_username"', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('name="_password"', $this->client->getResponse()->getContent());

        /* Submit Form */
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid credentials', $this->client->getResponse()->getContent());

        $form['_username'] = 'Admin';
        $form['_password'] = 'adminadmin';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('Bienvenue sur Todo List', $this->client->getResponse()->getContent());

        $form['_username'] = 'anonymous';
        $form['_password'] = 'anonymous_password';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertStringContainsString('is disabled and cannot log in', $this->client->getResponse()->getContent());
    }

    public function testLogOut()
    {
        $rep = $this->em->getRepository(User::class);
        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/');
        $link = $crawler->selectLink('Se déconnecter')->link();
        $this->client->click($link);
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }


}
