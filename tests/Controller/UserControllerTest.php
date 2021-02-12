<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListUser()
    {
        $client = static::createClient();

        // Request the Login Page
        $crawler = $client->request('GET', '/login');

        // Login to Admin User
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin',
            '_password' => 'pass',
        ]);;
        $client->submit($form);

        // Request the User List Page
        $crawler = $client->request('GET', '/users');

        // Get Status 200 in User List Page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Contains the String
        $this->assertStringContainsString(
            "Liste des utilisateurs",
            // Enter the Filter of the html class
            $crawler->filter('.container h1')->text()
        );
    }

    public function testCreateUser()
    {
        $client = static::createClient();

        // Get the Create User Page
        $crawler = $client->request('GET', '/users/create');

        // Add data in Form of the new User
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'usertest',
            'user[password][first]' => 'pass',
            'user[password][second]' => 'pass',
            'user[email]' => 'usertest@email.com',
        ]);
        $client->submit($form);

        // Get the Response
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Add filter "needle: L'utilisateur a bien été ajouté." & "selector: .alert-success"
        $this->assertStringContainsString(
            "L'utilisateur a bien été ajouté.",
            $crawler->filter('.alert-success')->text()
        );
    }

    public function testEditUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin',
            '_password' => 'pass',
        ]);
        $client->submit($form);

        $crawler = $client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'usertestedit',
            'user[password][first]' => 'passedit',
            'user[password][second]' => 'passedit',
            'user[email]' => 'usertestedit@email.com',
        ]);
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

    }
}