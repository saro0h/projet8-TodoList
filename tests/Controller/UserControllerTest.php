<?php

namespace App\Tests\Controller;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;//reload les fixtures
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    private $client;
    
    public function SetUp(): void
    {
        $this->client = static::createClient();
    }

    public function LoginAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['_username' => 'Durand', '_password' => 'password']);
    }

    public function LoginUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['_username' => 'utilisateur', '_password' => 'password']);
    }

    // public function ULoginUserAnonyme(): void
    // {
    //     $crawler = $this->client->request('GET', '/login');
    //     $form = $crawler->selectButton('Se connecter')->form();
    //     $this->client->submit($form, ['_username' => 'anonyme', '_password' => 'password']);
    // }

    public function testAccessToUsersListAsAdminOk()
    {
        $this->LoginAdmin();
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessToUsersListAsUserShouldThrow403Error()
    {
        $this->LoginUser();
        $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessToUsersListAsAnonymeShouldRedirectToLoginPage()
    {
        
        $this->client->request('GET', '/users');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserAction()
    {
        $this->LoginAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'utilisateur2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'user@y.fr';
        $form['user[roles]']->select("ROLE_ADMIN");
        
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testEditUserAction()
    {
        $this->LoginAdmin();

        $crawler = $this->client->request('GET', '/users/2/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'utilisateur2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'user@y.fr';
        $form['user[roles]']->select("ROLE_ADMIN");
        
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}
