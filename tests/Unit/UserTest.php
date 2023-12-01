<?php

namespace Tests\Unit;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User */
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testSetUsername()
    {
        $this->user->setUsername('test');
        $this->assertSame('test', $this->user->getUsername());
    }

    public function testSetPassword()
    {
        $this->user->setPassword('testpassword');
        $this->assertSame('testpassword', $this->user->getPassword());
    }

    public function testSetEmail()
    {
        $this->user->setEmail('test@gmail.com');
        $this->assertSame('test@gmail.com', $this->user->getEmail());
    }
}