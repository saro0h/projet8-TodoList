<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskDateIsValid(): void
    {
        $task = new Task();
        $date = new \DateTime();
        $task->setCreatedAt($date);
        self::assertEquals($date, $task->getCreatedAt());
    }
}
