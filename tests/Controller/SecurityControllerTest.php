<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('login');
    }

    public function testLoginWithUnknownUsername(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'unknown',
            '_password' => 'password',
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithWrongPassword(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $user->getUsername(),
            '_password' => 'azeazeazeaze',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();
        $this->assertSelectorTextSame('.alert.alert-danger', 'Nom utilisateur ou mot de passe incorrect !');
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $user->getUsername(),
            '_password' => 'password',
        ]);

        $client->submit($form);

        $client->followRedirect();
        $this->assertSelectorTextSame('.alert.alert-success', 'Vous vous êtes connecté !');
    }

    public function testLogout(): void
    {
        $client = static::createAuthenticatedUser();
        $client->followRedirects();
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Se déconnecter')->link();

        $client->click($link);

        $this->assertSelectorTextSame('.alert.alert-success', 'Vous êtes déconnecté !');
    }
}
