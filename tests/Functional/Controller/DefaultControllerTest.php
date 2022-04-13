<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractWebTestCase;

class DefaultControllerTest extends AbstractWebTestCase
{
    public function testIndexAnonymous()
    {
        $this->testIndex(302);
    }

    public function testIndexLogged()
    {
        $this->loginAs('user@user.com');
        $this->testIndex(200);
    }

    protected function testIndex(int $expectedCode)
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals($expectedCode, $this->client->getResponse()->getStatusCode());
        //$this->assertStringContainsString('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
