<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Tests\Unit\AbstractKernelTestCase;

class UserEntityTest extends AbstractKernelTestCase
{
    public function testUserEntityIsValid (): void
    {
        $user = new User();
        $user
            ->setEmail("test@gmail.com")
            ->setPassword("test")
            ->setUsername('test')
            ->setRoles([])
        ;

        $errors = $this->validator->validate($user);
        $this->assertCount(0, $errors);
    }
}
