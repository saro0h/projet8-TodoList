<?php

declare(strict_types=1);

namespace Tests\units\App\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterSetters() {
        $user = new User;
        $user->setUsername('Test username');
        $user->setEmail('email@test.fr');
        $user->setPassword('testPassword');
        $this->assertEquals('Test username', $user->getUsername());;
        $this->assertEquals('email@test.fr', $user->getEmail());
        $this->assertEquals('testPassword', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
