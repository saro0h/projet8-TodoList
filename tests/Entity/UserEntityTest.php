<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    private const EMAIL_CONSTRAINT_MESSAGE = 'Le format de l\'adresse n\'est pas correcte.';
    private const NOT_BLANK_CONSTRAINT_EMAIL_MESSAGE = 'Vous devez saisir une adresse email.';
    private const EXIST_CONSTRAINT_EMAIL_MESSAGE = 'Il existe déjà un compte avec cet adresse email.';
    private const INVALID_EMAIL_VALUE = 'admin@todo';
    private const INVALID_EMAIL_EXIST_VALUE = 'admin@todo.fr';
    private const VALID_EMAIL_VALUE = 'admin2@todo.fr';
    private const VALID_USERNAME_VALUE = 'admin2';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer(ValidatorInterface::class)->get('validator');
    }

    public function testUserEntityIsValid(): void
    {
        $user = new User();
        $user
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword('password')
            ->setUsername(self::VALID_USERNAME_VALUE);

        $this->getValidationErrors($user, 0);
    }

    /**
     * @dataProvider provideDataToTestEmailConstraints 
     */
    public function testUserEntityIsInvalidEmail(string $email, string $message): void
    {
        $user = new User();
        $user->setEmail($email)
            ->setPassword('password')
            ->setUsername(self::VALID_USERNAME_VALUE);

        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals($message, $errors[0]->getMessage());
    }

    public function provideDataToTestEmailConstraints(): array
    {
        return [
            ['', self::NOT_BLANK_CONSTRAINT_EMAIL_MESSAGE],
            [self::INVALID_EMAIL_VALUE, self::EMAIL_CONSTRAINT_MESSAGE],
            [self::INVALID_EMAIL_EXIST_VALUE, self::EXIST_CONSTRAINT_EMAIL_MESSAGE],
        ];
    }

    /**
     *  @dataProvider provideInvalidUsernames 
     */
    public function testUserEntityIsInvalidBadUsernameEntered(string $invalidUsername, string $message, int $numberExcept): void
    {
        $user = new User();
        $user
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword('password')
            ->setUsername($invalidUsername);

        $errors = $this->getValidationErrors($user, $numberExcept);

        $this->assertEquals($message, $errors[0]->getMessage());
    }

    public function provideInvalidUsernames(): array
    {
        return [
            ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Le nom utilisateur doit comporter au maximum 50 caractères.', 1],
            ['aa', 'Le nom utilisateur doit comporter au moins 3 caractères.', 1],
            ['', 'Vous devez saisir un nom d\'utilisateur.', 2],
            ['admin', 'Ce nom utilisateur existe déjà.', 1]
        ];
    }

    private function getValidationErrors(User $user, int $numberExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberExpectedErrors, $errors);

        return $errors;
    }
}
