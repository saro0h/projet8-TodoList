<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class UserEntityTest extends TestCase
{
    public function testIsNull()
    {
        $user = new User();
        $this->assertEmpty($user->getUsername());
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getTasks());
    }
}
