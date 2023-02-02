<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseController
{
    public function testUserListpageWithAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testUserListpageWithLoggedUser(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/users');

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUserListpageWithLoggedAdmin(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/users');

        self::assertResponseIsSuccessful();
    }

    public function testUserCreatepageWithLoggedAdminWithFakeData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/users/create');

        $client->submitForm('Ajouter', [
            'user[username]' => '',
            'user[password][first]' => '',
            'user[password][second]' => '',
            'user[email]' => '',
            'user[roles]' => 'ROLE_USER',
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testUserCreatepageWithLoggedAdminWithGoodData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/users/create');

        $client->submitForm('Ajouter', [
            'user[username]' => 'toto',
            'user[password][first]' => 'pass',
            'user[password][second]' => 'pass',
            'user[email]' => 'toto@todolist.com',
            'user[roles]' => 'ROLE_USER',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUserEditpageWithLoggedAdminWithGoodData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/users/2/edit');

        $client->submitForm('Modifier', [
            'user[username]' => BaseController::USER_EMAIL,
            'user[password][first]' => 'pass updated',
            'user[password][second]' => 'pass updated',
            'user[email]' => 'userup@todolist.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
