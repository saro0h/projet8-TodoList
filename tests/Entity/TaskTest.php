<?php

namespace Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testClass()
    {
        $user = new User();
        $user->setUsername('Name');

        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('Content');
        $task->setUser($user);
        $task->setCreatedAt('21/03/1980');
        $task->toggle(true);

        $this->assertEquals('Title', $task->getTitle());
        $this->assertEquals('Content', $task->getContent());
        $this->assertEquals('21/03/1980', $task->getCreatedAt());
        $this->assertEquals(true, $task->isDone());
        $this->assertEquals($user, $task->getUser());
    }


}
