<?php


namespace App\Tests\Entity;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    private $user;

    public function setUp(): void
    {
        $this->user = new User();
    }

    public function testUsername()
    {
        $this->user->setUsername('name');
        $this->assertSame('name', $this->user->getUsername());
    }

    public function testPassword()
    {
        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());
    }

    public function testEmail()
    {
        $this->user->setEmail('name@name.fr');
        $this->assertSame('name@name.fr', $this->user->getEmail());
    }

    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }
}
