<?php

namespace Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    /** Test Homepage without user connected */
    public function testIndexAsNotloggedUser()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /** Test Homepage with user connected */
    public function testIndexAsNormalUser(){
        $client = static::createClient();
        /** @var EntityManager $em */
        $em= static::$container->get(EntityManagerInterface::class);

        $rep = $em->getRepository(User::class);

        $testUser = $rep->findOneBy(['username' => 'Admin']);
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}