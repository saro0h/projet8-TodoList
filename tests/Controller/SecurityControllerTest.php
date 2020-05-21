<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends BaseController
{
    public function testLoginpage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}