<?php

namespace App\Tests\Controller;

class SecurityControllerTest extends BaseController
{
    public function testLoginpage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
    }
}
