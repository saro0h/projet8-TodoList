<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolantionList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    private const EMAIL_CONSTRAINT_MESSAGE = 'Le format de l\'adresse n\'est pas correcte.';
    private const NOT_BLANK_CONSTRAINT_EMAIL_MESSAGE = 'Vous devez saisir une adresse email.';
    private const INVALID_EMAIL_VALUE = 'admin@todo';
    private const VALID_EMAIL_VALUE = 'admin@todo.fr';
    private const VALID_USERNAME_VALUE = 'admin';

    private ValidatorInterface $validator;

    protected function setUp(): void 
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserEntityIsValid(): void 
    {
        $user = new User();
        $user
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword('password')
            ->setUsername(self:VALID_USERNAME_VALUE);
        
        $this->getValidationErrors($user, 0);
    }

    public function testUserEntityIsInvalidNoEmailEntered(): void 
    {
        $user = new User();
        $user
            ->setPassword('password')
            ->setUsername(self:VALID_USERNAME_VALUE);
        
        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::NOT_BLANK_CONSTRAINT_EMAIL_MESSAGE, $errors[0]->getMessage());
    }

    public function testUserEntityIsInvalidWrongEmailEntered(): void 
    {
        $user = new User();
        $user
            ->setUsername(self:INVALID_EMAIL_VALUE);
            ->setPassword('password')
            ->setUsername(self:VALID_USERNAME_VALUE);
        
        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::EMAIL_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }


    /**
     *  @dataProvider provideInvalidUsernames 
     */
    public function testUserEntityIsInvalidBadUsernameEntered(string $invalidUsername, string $message): void 
    {
        $user = new User();
        $user
            ->setUsername(self:VALID_EMAIL_VALUE);
            ->setPassword('password')
            ->setUsername($invalidUsername);
        
        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals($message, $errors[0]->getMessage());
    }

    public function provideInvalidUsernames(): array
    {
        return [
            ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Le nom utilisateur doit comporter au maximum "50" caractères.'],
            ['aa', 'Le nom utilisateur doit comporter au moins "3" caractères.'],
            ['', 'Vous devez saisir un nom d\'utilisateur.']
        ];
    }

    private function getValidationErrors(User $user, int $numberExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberExpectedErrors, $errors);

        return $errors;
    }
}