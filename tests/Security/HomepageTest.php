<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Generator;
use App\Repository\UserRepository;

class HoempageTest extends WebTestCase
{
    /**
     * @dataProvider provideUri
     * @param string $uri
     */
    public function testIndex(string $uri): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('jean@sf.com');
        $client->loginUser($testUser);

        $client->request(Request::METHOD_GET, $uri);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }

    /**
     * @return Generator
     */
    public function provideUri(): Generator
    {
        yield ['/'];
    }
}
