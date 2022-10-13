<?php

namespace App\Tests\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomePage()
    {
        $client = static::createClient();//on simule un navigateur
        $client->request('GET', '/');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }


//     private KernelBrowser|null $client = null;

//     public function setUp() : void
//     {
//         $this->client = static::createClient();
//     }
//     public function testHomepage()
//     {

//         $urlGenerator = $this->client->getContainer()->get('router.default');
//         $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));
//     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//  }
}
