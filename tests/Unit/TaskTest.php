<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

    private $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new Task();
    }

    public function testSetTitle()
    {
        $this->task->setTitle('New Task');
        $this->assertEquals('New Task', $this->task->getTitle());
    }

    public function testSetContent()
    {
        $this->task->setContent('Content new task');
        $this->assertEquals('Content new task', $this->task->getContent());
    }

    public function testSetCreatedAt()
    {
        $now = new \DateTime();
        $this->task->setCreatedAt($now);
        $this->assertEquals($now, $this->task->getCreatedAt());
    }

    public function testIsDone()
    {
        $this->task->toggle(true);
        $this->assertTrue($this->task->isDone());
    }
}