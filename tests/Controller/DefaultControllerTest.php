<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends BaseController
{
    public function testHomepage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testHomepageWithLoggedUser()
    {
        $client = $this->login('user@todolist.com');
        $client->request('GET', '/');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testHomepageWithLoggedAdmin()
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
