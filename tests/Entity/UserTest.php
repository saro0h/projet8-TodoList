<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testTaskIsValid()
    {
        $task = new Task();
        $user = new User();
        $user->addTask($task);
        $user->removeTask($task);
        self::assertEquals(0, count($user->getTasks()));
    }

    public function testRolesAreValid()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        self::assertGreaterThan(0, count($user->getRoles()));
    }

    public function testRolesWithRoleUserAreValid()
    {
        $user = new User();
        self::assertGreaterThan(0, count($user->getRoles()));
    }
}
