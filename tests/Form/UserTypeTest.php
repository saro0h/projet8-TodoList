<?php


namespace App\Tests\Form;


use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testCreateUserSuccess()
    {
        $formData = [
            'username' => 'pseudo',
            'email' => 'pseudo@email.com',
            'password' => array('first' => 'pass', 'second' => 'pass'),
            'roles' => 'ROLE_USER'
        ];

        $model = new User();

        $form = $this->factory->create(UserType::class, $model);

        $excepted = new User();
        $excepted->setUsername('pseudo');
        $excepted->setEmail('pseudo@email.com');
        $excepted->setPassword('pass');
        $excepted->setRoles(array("ROLE_USER"));

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($excepted, $model);
    }

    public function testCreateUserNotSuccess()
    {
        $formData = [
            'username' => 'pseudo',
            'email' => 'pseudo@email.com',
            'password' => array('first' => 'truepass', 'second' => 'falsepass'),
            'roles' => 'ROLE_USER'
        ];

        $model = new User();

        $form = $this->factory->create(UserType::class, $model);

        $excepted = new User();
        $excepted->setUsername('pseudo');
        $excepted->setEmail('pseudo@email.com');
        $excepted->setPassword(NULL);
        $excepted->setRoles(array("ROLE_USER"));

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($excepted, $model);
    }
}