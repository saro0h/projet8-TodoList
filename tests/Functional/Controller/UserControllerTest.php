<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class UserControllerTest extends AbstractWebTestCase
{
    const PAGES = [
        '/users',
        '/users/create'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testAsAdmin()
    {
        $this->loginAs('admin@admin.com');

        foreach (self::PAGES as $page) {
            $this->testPage($page);
        }
    }

    public function testAsUser()
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
            $this->assertNotEquals(200, $this->client->getResponse()->getStatusCode());
        }
    }
}
