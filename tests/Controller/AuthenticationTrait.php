<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
trait AuthenticationTrait
{
    /**
     * @var User
     */
    private $user;
    /**
     * Crée un utilisateur authentifié
     * @param string $username
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    public function createAuthenticatedClient( $username = 'admin')
    {
        $client = static::createClient();
        $session = $client->getContainer()->get('session');
        $doctrine = $client->getContainer()->get('doctrine.orm.entity_manager');
        $firewallName = 'main';
        $this->user = $doctrine->getRepository(User::class)->findOneByUsername($username);
        $token = new UsernamePasswordToken($this->user, null, $firewallName, $this->user->getRoles());
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $client;
    }
    public function getUser()
    {
        return $this->user;
    }
}