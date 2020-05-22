<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskDateIsValid()
    {
        $task = new Task();
        $date = new \DateTime();
        $task->setCreatedAt($date);
        $this->assertEquals($date, $task->getCreatedAt());
    }
}
