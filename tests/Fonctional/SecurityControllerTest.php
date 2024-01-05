<?php

namespace App\Tests\Fonctional;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class SecurityControllerTest extends WebTestCase
{

    private $client;

    /** @var EntityManager */
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testLoginSuccess()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('input[name="_username"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="_password"]')->count());

        $form = $crawler->selectButton('Se connecter')->form();
        $formValues = [
            '_username' => 'nonoland',
            '_password' => 'test'
        ];
        $this->client->submit($form, $formValues);

        $this->assertResponseRedirects('/');

        $this->client->followRedirect();

        $this->assertTrue($this->client->getContainer()->get('security.helper')->isGranted('ROLE_USER'));
    }

    public function testLoginFailed()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('input[name="_username"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="_password"]')->count());

        $form = $crawler->selectButton('Se connecter')->form();
        $formValues = [
            '_username' => 'nonoland',
            '_password' => 'testt'
        ];
        $this->client->submit($form, $formValues);

        $this->client->followRedirect();

        $this->assertEquals('/login', $this->client->getRequest()->getPathInfo());
        $this->assertFalse($this->client->getContainer()->get('security.helper')->isGranted('ROLE_USER'));
    }
}
