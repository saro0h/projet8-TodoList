<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseController
{
    public function testUserListpageWithAnonymousUser()
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUserListpageWithLoggedUser()
    {
        $client = $this->login('user', 'pass');
        $client->request('GET', '/users');

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUserListpageWithLoggedAdmin()
    {
        $client = $this->login('admin', 'pass');
        $client->request('GET', '/users');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testUserCreatepageWithLoggedAdminWithFakeData()
    {
        $client = $this->login('admin', 'pass');
        $client->request('GET', '/users/create');

        $client->submitForm('Ajouter', [
            'user[username]' => '',
            'user[password][first]' => '',
            'user[password][second]' => '',
            'user[email]' => '',
            'user[roles]' => 'ROLE_USER',
        ]);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testUserCreatepageWithLoggedAdminWithGoodData()
    {
        $client = $this->login('admin', 'pass');
        $client->request('GET', '/users/create');

        $client->submitForm('Ajouter', [
            'user[username]' => 'toto',
            'user[password][first]' => 'pass',
            'user[password][second]' => 'pass',
            'user[email]' => 'toto@todolist.com',
            'user[roles]' => 'ROLE_USER',
        ]);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUserEditpageWithLoggedAdminWithGoodData()
    {
        $client = $this->login('admin', 'pass');
        $client->request('GET', '/users/2/edit');

        $client->submitForm('Modifier', [
            'user[username]' => 'user',
            'user[password][first]' => 'pass updated',
            'user[password][second]' => 'pass updated',
            'user[email]' => 'userup@todolist.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}