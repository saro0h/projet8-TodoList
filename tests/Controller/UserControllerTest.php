<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testShowListUsers(): void
    {
        $client = static::createAuthenticatedAdmin();

        $crawler = $client->request('GET', '/users');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Liste des utilisateurs")')->count()
        );
    }

    public function testShowEditUser(): void
    {
        $client = static::createAuthenticatedAdmin();

        $userRepository = static::getContainer()->get(UserRepository::class);
        // DESC TO AVOID "Anonymous User" of Fixtures DATA
        $userToEdit = $userRepository->findOneBy([], ['id' => 'DESC']);

        $crawler = $client->request('GET', '/user/' . $userToEdit->getId());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Modifier")')->count()
        );
    }
}
