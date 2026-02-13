<?php

declare(strict_types=1);

namespace Tests;

use App\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseWebTestCase extends WebTestCase
{
    protected $client = null;

    public function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();
    }

    protected function createAuthenticatedClient(string $email = 'user@test.fr'): KernelBrowser
    {
        if ($this->client === null) {
            $this->client = static::createClient();
        }

        $user = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new RuntimeException("Utilisateur avec l'email {$email} introuvable.");
        }

        $this->client->loginUser($user);

        return $this->client;
    }

    protected function generateUrl(string $route, array $params = []): string
    {
        return $this->client->getContainer()->get('router')->generate($route, $params);
    }
}
