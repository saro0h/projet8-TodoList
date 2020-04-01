<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testHomepageWithLoggedUser()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'toto',
            'PHP_AUTH_PW'   => 'pass',
        ]);
        $client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}