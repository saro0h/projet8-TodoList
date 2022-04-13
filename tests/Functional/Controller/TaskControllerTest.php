<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{
    public function testTaskList(): void
    {
        $this->loginAs('user@user.com');
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //$this->assertStringContainsString('Welcome to Symfony', $crawler->filter('#container h1')->text());

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }
}
