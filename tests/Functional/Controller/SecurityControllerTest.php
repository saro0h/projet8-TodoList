<?php

namespace App\Tests\Functional\Controller;

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
        $this->assertSelectorExists('.alert.alert-danger');
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

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testLogout(): void
    {
        $client = static::createAuthenticatedUser();

        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Se déconnecter')->link();

        $client->click($link);

        $this->assertRouteSame('logout');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }
}
