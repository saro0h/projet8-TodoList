<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * test unitaire de la class User
     */
    public function testUser()
    {
        $user = new User();
        $task =new Task();
        $this->assertNull($user->getId());
        $user->setUsername('test');
        $this->assertSame('test', $user->getUsername());
        $user->setPassword('test');
        $this->assertSame('test', $user->getPassword());
        $user->setEmail('test@symfony.com');
        $this->assertSame('test@symfony.com', $user->getEmail());
        $user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $user->addTask($task);
        //$this->assertInstanceOf(Task::class, $user->getTasks());
        $this->assertNotEmpty($user->getTasks());
        $this->assertNull($user->getSalt());
    }
}