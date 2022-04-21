<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = static::getContainer()->get(RouterInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    protected function loginAs(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $this->client->loginUser($user);
        return $user;
    }

    protected function testEntityList(?string $loginAs, $routeName, bool $expected = true): void
    {
        if ($loginAs) {
            $this->loginAs($loginAs);
        }

        $crawler = $this->client->request('GET', $this->router->generate($routeName));

        if ($expected) {
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
            $this->assertResponseIsSuccessful();
        } else {
            $this->assertNotEquals(200, $this->client->getResponse()->getStatusCode());
        }
    }
}