<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Task;

class UserTest extends \PHPUnit\Framework\TestCase
{
    private $user;

    public function setUp(): void
    {
        $this->user = new User();
    }

    public function testId()
    {
        $this->assertNull($this->user->getId());
    }

    public function testUsername()
    {
        $this->assertNull($this->user->getUsername());

        $this->user->setUsername('simon');
        $this->assertSame('simon', $this->user->getUsername());
    }

    public function testPassword()
    {
        $this->assertNull($this->user->getPassword());

        $this->user->setPassword('secret');
        $this->assertSame('secret', $this->user->getPassword());
    }

    public function testEmail()
    {
        $this->assertNull($this->user->getEmail());

        $this->user->setEmail('simon.charbonnier@gmail.com');
        $this->assertSame('simon.charbonnier@gmail.com', $this->user->getEmail());
    }

    public function testTasks()
    {
        $this->assertEmpty($this->user->getTasks());

        $task = new Task();
        $this->user->addTask($task);
        $this->assertContains($task, $this->user->getTasks());

        $this->user->removeTask($task);
        $this->assertEmpty($this->user->getTasks());
    }

    public function testRoles()
    {
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());

        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testSalt()
    {
        $this->assertNull($this->user->getSalt());
    }

    public function testEraseCredential()
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    public function testUserIdentifier()
    {
        $this->user->setUsername('simon');
        $this->assertSame('simon', $this->user->getUserIdentifier());
    }
}
