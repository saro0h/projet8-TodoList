<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private \DOMDocument $domId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->domId = new \DOMDocument();
    }

    public function testLoginOk(): void
    {
        $loginForm = $this->client->request('GET', "/login")->selectButton("Sign in")->form();

        $loginForm['username'] = 'user1';
        $loginForm['password'] = 'coucou';

        $this->client->submit($loginForm);

        $this->client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('logoutButton'), 'Se déconnecter');
    }

    public function testLoginNotOk(): void
    {
        $loginForm = $this->client->request('GET', "/login")->selectButton("Sign in")->form();

        $loginForm['username'] = 'user';
        $loginForm['password'] = 'coucou';

        $this->client->submit($loginForm);

        $this->client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('invalidCredentials'), 'Invalid credentials.');
    }

    public function testLogout(): void
    {
        $this->client->request('GET', "/logout");

        $this->client->followRedirect();

        $this->assertSelectorTextContains((string)$this->domId->getElementById('loginButton'), 'Se connecter');
    }
}
