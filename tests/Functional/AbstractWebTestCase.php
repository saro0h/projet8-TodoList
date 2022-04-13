<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
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
}