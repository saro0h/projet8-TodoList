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
            '_username' => 'testa',
            '_password' => 'test',
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
            'user[username]' => 'newUser',
            'user[password][first]' => 'pass1',
            'user[password][second]' => 'pass1',
            'user[email]' => 'newUser@email.fr',
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
            '_username' => 'testa',
            '_password' => 'test',
        ]);;
        $client->submit($form);

        $crawler = $client->request('GET', '/users/1/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'userEdit',
            'user[password][first]' => 'pass1',
            'user[password][second]' => 'pass1',
            'user[email]' => 'userEdit@email.fr',
        ]);
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            "Superbe ! L'utilisateur a bien été modifié",
            $crawler->filter('.alert-success')->text()
        );
    }
}