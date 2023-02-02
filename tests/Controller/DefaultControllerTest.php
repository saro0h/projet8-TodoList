<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends BaseController
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testHomepageWithLoggedUser(): void
    {
        $client = $this->login('user@todolist.com');
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }

    public function testHomepageWithLoggedAdmin(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
