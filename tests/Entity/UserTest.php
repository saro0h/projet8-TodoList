<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    private $user;
    private $task;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
    }

    public function testId(): void
    {
        $this->assertNull($this->user->getId());
    }

    public function testUsername(): void
    {
        $this->user->setUsername('name');
        $this->assertSame('name', $this->user->getUsername());
    }

    public function testPassword(): void
    {
        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());
    }

    public function testSalt(): void
    {
        $this->assertNull($this->user->getSalt());
    }

    public function testEraseCredentials(): void
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    public function testEmail(): void
    {
        $this->user->setEmail('name@name.fr');
        $this->assertSame('name@name.fr', $this->user->getEmail());
    }

    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    public function testTask()
    {
        $this->user->addTask($this->task);
        $this->assertCount(1, $this->user->getTasks());

        $tasks = $this->user->getTasks();
        $this->assertSame($this->user->getTasks(), $tasks);

        $this->user->removeTask($this->task);
        $this->assertCount(0, $this->user->getTasks());
    }
}
