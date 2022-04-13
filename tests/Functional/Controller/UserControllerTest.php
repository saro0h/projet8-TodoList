<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    const PAGES = [
        '/users',
        '/users/create'
    ];

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    protected function loginAs(string $email): KernelBrowser
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        return $this->client->loginUser($user);
    }

    public function test()
    {
        $this->testAsAdmin();
        $this->testAsUser();
    }

    protected function testAsAdmin()
    {
        $this->loginAs('admin@admin.com');

        foreach (self::PAGES as $page) {
            $this->testPage($page);
        }
    }

    protected function testAsUser()
    {
        $this->loginAs('user@user.com');

        foreach (self::PAGES as $page) {
            $this->testPage($page, false);
        }
    }

    protected function testPage(string $page, bool $expected = true)
    {
        $this->client->request('GET', $page);
        if($expected) {
            $this->assertResponseIsSuccessful();
        }
        else {
            $this->assertResponseIsUnprocessable();
        }
    }
}
