<?php

namespace Tests\Fonctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexForAnonymousUser()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testIndexForAuthenticatedUser()
    {
        $client = self::createAuthenticationClient();

        $client->request('GET', '/');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    //TODO Test de connexion avec le formulaire

    public static function createAuthenticationClient($username = "nonoland", $password = "test")
    {
        return static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password
        ]);
    }
}
