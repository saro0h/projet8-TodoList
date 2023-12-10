<?php

namespace Fonctional;

use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\Fonctional\DefaultControllerTest;

class SecurityControllerTest extends WebTestCase
{

    private $client;

    /** @var EntityManager */
    private $entityManager;

    protected function setUp()
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

        $this->client->followRedirect();

        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());

        /** @var Session $session */
        $session = $this->client->getContainer()->get('session');
        $securityToken = $session->get('_security_main');

        $this->assertNotNull($securityToken);

        /** @var UsernamePasswordToken $token */
        $token = unserialize($securityToken);

        $this->assertTrue($token->getUsername() == 'nonoland');
    }

    public function testLoginFailed()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('input[name="_username"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name="_password"]')->count());

        $form = $crawler->selectButton('Se connecter')->form();
        $formValues = [
            '_username' => 'nonolandd',
            '_password' => 'test'
        ];
        $this->client->submit($form, $formValues);

        $this->client->followRedirect();

        $this->assertEquals('/login', $this->client->getRequest()->getPathInfo());

        /** @var Session $session */
        $session = $this->client->getContainer()->get('session');
        $securityToken = $session->get('_security_main');

        $this->assertNull($securityToken);
    }
}
