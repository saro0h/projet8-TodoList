<?php

declare(strict_types=1);

namespace Tests\units\App\Entity;

use App\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetterSettersAndToggle() {
        $task = new Task;
        $task->setTitle('Test task');
        $task->setContent('Test content');
        $task->setCreatedAt(new DateTime("1988-10-17"));
        $this->assertEquals('Test task', $task->getTitle());
        $this->assertEquals('Test content', $task->getContent());
        $this->assertEquals(new DateTime("1988-10-17"), $task->getCreatedAt());
        $isDone = $task->isDone();
        $task->toggle(!$isDone);
        $this->assertNotEquals($isDone, $task->isDone());
    }
}
