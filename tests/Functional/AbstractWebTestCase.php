<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    protected function loginAs(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $this->client->loginUser($user);
        return $user;
    }
}