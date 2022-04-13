<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

class UserTest extends KernelTestCase
{
    private const EMAIL_NOT_BLANK_MESSAGE = 'Vous devez saisir une adresse email.';
    private const EMAIL_CONSTRAINT_MESSAGE = "Le format de l'adresse n'est pas correcte.";

    private const VALID_EMAIL_VALUE = "test@gmail.com";
    private const VALID_PASSWORD_VALUE = "test";

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserEntityIsValid (): void
    {
        $user = new User();
        $user->setEmail(self::VALID_EMAIL_VALUE)->setPassword(self::VALID_PASSWORD_VALUE);

        $errors = $this->validator->validate($user);
        $this->assertCount(0, $errors);
    }

//    public function testSomething(): void
//    {
//        $kernel = self::bootKernel();
//
//        $this->assertSame('test', $kernel->getEnvironment());
//        //$routerService = static::getContainer()->get('router');
//        //$myCustomService = static::getContainer()->get(CustomService::class);
//    }
}
