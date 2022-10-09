<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticationTrait
{
    public static function createAuthenticatedUser(): KernelBrowser
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        return $client;
    }

    public static function createAuthenticatedAdmin(): KernelBrowser
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneByRole('ROLE_ADMIN');
        $client->loginUser($user);

        return $client;
    }
}
