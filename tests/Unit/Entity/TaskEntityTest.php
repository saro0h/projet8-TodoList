<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Tests\Unit\AbstractKernelTestCase;

class TaskEntityTest extends AbstractKernelTestCase
{
    public function testTaskEntityIsValid (): void
    {
        $task = new Task();
        $task
            ->setTitle('task one')
            ->setContent('content task one')
            ->setUser(null)
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $errors = $this->validator->validate($task);
        $this->assertCount(0, $errors);
    }
}
