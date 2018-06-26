<?php

namespace App\Tests\Controller;

use App\Tests\Authentication;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest
 * @package App\Tests\Controller
 */
class SecurityControllerTest extends WebTestCase
{
    use Authentication;

    public function testLogin()
    {
        $client = $this->login();

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("Se déconnecter")')->count());
    }
}