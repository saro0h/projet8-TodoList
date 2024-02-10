<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;

class TaskTest extends \PHPUnit\Framework\TestCase
{
    private $task;

    public function setUp(): void
    {
        $this->task = new Task();
    }

    public function testId()
    {
        $this->assertNull($this->task->getId());
    }

    public function testCreatedAt()
    {
        $this->assertInstanceOf(\DateTime::class, $this->task->getCreatedAt());

        $datetime = new \DateTime;
        $this->task->setCreatedAt($datetime);
        $this->assertSame($datetime, $this->task->getCreatedAt());
    }

    public function testIsDone()
    {
        $this->assertFalse($this->task->isDone());

        $this->task->toggle(true);
        $this->assertTrue($this->task->isDone());
    }

    public function testTitle()
    {
        $this->assertNull($this->task->getTitle());

        $this->task->setTitle('Test title');
        $this->assertSame('Test title', $this->task->getTitle());
    }

    public function testContent()
    {
        $this->assertNull($this->task->getContent());

        $this->task->setContent('Test content');
        $this->assertSame('Test content', $this->task->getContent());
    }

    public function testUser()
    {
        $this->assertNull($this->task->getUser());

        $user = new User();
        $this->task->setUser($user);
        $this->assertSame($user, $this->task->getUser());
    }
}
