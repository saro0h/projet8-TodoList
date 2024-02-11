<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use App\Form\UserType;
use App\Entity\User;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'test',
            'password' => ['first' => 'secret', 'second' => 'secret'],
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER']
        ];

        $expectedUser = new User();
        $expectedUser->setUsername($formData['username']);
        $expectedUser->setPassword($formData['password']['first']);
        $expectedUser->setEmail($formData['email']);
        $expectedUser->setRoles($formData['roles']);

        $user = new User();
        $form = $this->factory->create(UserType::class, $user);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedUser->getUsername(), $form->get('username')->getData());
        $this->assertEquals($expectedUser->getPassword(), $form->get('password')->getData());
        $this->assertEquals($expectedUser->getEmail(), $form->get('email')->getData());
        $this->assertEquals($expectedUser->getRoles(), $form->get('roles')->getData());

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
