<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\Security\Core\Security;

class SecurityControllerTest extends AbstractWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->security = static::getContainer()->get(Security::class);
        $this->userRepo = static::getContainer()->get(UserRepository::class);
    }

    public function testLoginPage(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testLogin()
    {
        $crawler = $this->client->request('GET', "/login");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Se connecter')->form();
        $form['email']->setValue('user@user.com');
        $form['password']->setValue('user');
        $this->client->submit($form);

        $user = $this->security->getUser();

        $this->assertNotEquals('user@user.com', $user->getEmail());
    }
}
