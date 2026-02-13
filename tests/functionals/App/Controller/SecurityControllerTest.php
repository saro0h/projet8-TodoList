<?php

declare(strict_types=1);

namespace Tests\functionals\App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class SecurityControllerTest extends BaseWebTestCase
{
    public function testGetLoginPage()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('login'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString("_username", $crawler->filter('form')->html());
        $this->assertStringContainsString("_password", $crawler->filter('form')->html());
    }

    public function testLoginLogoutSuccessful()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Bienvenue sur Todo List', $crawler->filter('h1')->text());
        $this->assertStringContainsString('Se déconnecter', $crawler->filter('.btn-danger')->text());

        $logoutLink = $crawler->selectLink('Se déconnecter');

        $this->client->click($logoutLink->link());;
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Bienvenue sur Todo List', $crawler->filter('h1')->text());
        $this->assertStringContainsString('Se connecter', $crawler->filter('.btn-success')->text());
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user',
            '_password' => 'wrong_password',
        ]);
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Invalid credentials.', $crawler->filter('.alert-danger')->text());
    }
}
