<?php

namespace Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testClass()
    {
        $task = new Task();
        $task->setTitle('Title');

        $user = new User();
        $user->setUsername('username');
        $user->setRoles(['ROLE_TEST']);
        $user->setEmail('test@mail.com');
        $user->setPassword('password');
        $user->addTask($task);

        $this->assertEquals('username', $user->getUsername());
        $this->assertContains('ROLE_TEST', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertEquals('test@mail.com', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertContains($task, $user->getTasks());
    }

    public function testRemoveTasks()
    {
        $task = new Task();
        $task->setTitle('Title');

        $user = new User();
        $user->addTask($task);

        $user->removeTask($task);

        $this->assertNotContains($task, $user->getTasks());
        $this->assertEquals(null, $task->getUser());
    }

}
