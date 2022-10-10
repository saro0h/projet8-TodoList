<?php

namespace App\Tests\Entity;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;


class TaskTest extends TestCase

{

    public function testGetId()
    {
        $task = new Task();
        static::assertEquals($task->getId(), null);
    }

    public function testGetSetTitle()
    {
        $task = new Task();
        $task->setTitle('ExempleTitle');
        static::assertEquals($task->getTitle(), 'ExempleTitle');
    }

    public function testGetSetContent()
    {
        $task = new Task();
        $task->setContent('ExempleContent');
        static::assertEquals($task->getContent(), 'ExempleContent');
    }

    public function testGetSetCreatedAt()
    {
        $task = new Task();
        $task->setCreatedAt(new \DateTime);
        static::assertInstanceOf(\DateTime::class, $task->getCreatedAt());
    }

    public function testGetSetUser()
    {
        $task = new Task();
        $task->setUser(new User);
        static::assertInstanceOf(User::class, $task->getUser());
    }

    public function testIsDoneToggle()
    {
        $task = new Task();
        $task->toggle(true);
        static::assertEquals($task->isDone(), true);
    }
}
