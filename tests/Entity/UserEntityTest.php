<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{

    private function setUpUser ()
    {
        $user = new User();
        $user->setUsername('usertest');
        $user->setPassword('coucou');
        $user->setEmail('usertest@gmail.com');
        $user->setRoles(['ROLE_USER']);
        return $user;
    }

    private function setUpTask ()
    {
        $task = new Task ();
        $task->setTitle('titletest');
        $task->setContent('contenttest');
        $task->setIsdone(0);
        $task->setCreatedAt(new \DateTime('now'));

        return $task;
    }

    public function testGetTask ()
    {
        $user = $this->setUpUser();
        $task = $this->setUpTask();
        $user->addTask($task);

        self::assertNotNull($user->getTasks());
    }
    public function testAddTask ()
    {
        $user = $this->setUpUser();
        $task = $this->setUpTask();
        $user->addTask($task);

        self::assertEquals($user, $task->getAuthor());
    }

    public function testRemoveTask ()
    {
        $user = $this->setUpUser();
        $task = $this->setUpTask();
        $user->addTask($task);

        self::assertEquals($user, $task->getAuthor());

        $user->removeTask($task);

        self::assertTrue($task->getAuthor() == null);
    }
}