<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    protected function loginAs(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $this->client->loginUser($user);
    }

    public function testIndexAnonymous()
    {
        $this->testIndex(302);
    }

    public function testIndexLogged()
    {
        $this->loginAs('user@user.com');
        $this->testIndex(200);
    }

    protected function testIndex(int $expectedCode)
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals($expectedCode, $this->client->getResponse()->getStatusCode());
        //$this->assertStringContainsString('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
