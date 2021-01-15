<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'testa',
            '_password' => 'test',
        ]);;
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            "Se déconnecter",
            $crawler->filter('.btn-danger')->text()
        );
    }

    public function testLogout()
    {
        $client = static::createClient();

        // Request Login Page
        $crawler = $client->request('GET', '/login');

        // Login & Submit the Form
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'testa',
            '_password' => 'test',
        ]);;
        $client->submit($form);

        // Request the Home Page
        $client->request('GET', '/');

        // Click to Logout button
        $crawler = $client->clickLink('Se déconnecter');

        // Get Status 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        // Get Status 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Contains the String
        $this->assertStringContainsString(
            "Se connecter",
            // Enter the Filter of the html class
            $crawler->filter('.btn-success')->text()
        );
    }
}