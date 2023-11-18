<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexForAnonymousUser()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testIndexForAuthenticatedUser()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'nonoland',
            'PHP_AUTH_PW'   => 'test',
        ]);

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
