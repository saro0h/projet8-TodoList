<?php

namespace Tests\functionals\App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\BaseWebTestCase;

class DefaultControllerTest extends BaseWebTestCase
{
    public function testIndex()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List', $crawler->filter('h1')->text());
    }
}
