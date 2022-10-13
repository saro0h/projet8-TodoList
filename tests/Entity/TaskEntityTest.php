<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolantionList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskEntityTest extends KernelTestCase
{
    private const NOT_BLANK_CONSTRAINT_TITLE_MESSAGE = 'Vous devez saisir un titre.';
    private const NOT_BLANK_CONSTRAINT_CONTENT_MESSAGE = 'Vous devez saisir du contenu.';
    private User $author;
    private ValidatorInterface $validator;

    protected function setUp(): void 
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->author = new User();
    }

    public function testTaskEntityIsValid(): void 
    {
        $now = new \DateTimeImmutable();
        $task = new Task();
        $task
            ->setTitle('Un essai')
            ->setContent('Description de l\'essai')
            ->setCreatedAt($now)
            ->setAuthor($this->author);
        $this->getValidationErrors($task, 0);

        $this->assertSame($task->getTitle(), 'Un essai');
        $this->assertSame($task->getContent(), 'Description de l\'essai');
        $this->assertSame($task->getIsDone(), false);
        $this->assertSame($task->getAuthor(), $this->author);
        $this->assertSame($task->getCreatedAt(), $now);
    }

    public function testTaskIsNull(): void 
    {
        $task = new Task();
        $this->assertEmpty($task->getTitle());
        $this->assertEmpty($task->getContent());
        $this->assertEmpty($task->getAuthor());
    }

    public function testTaskEntityIsInvalidNoTitleEntered(): void 
    {
        $task = new Task();
        $task
            ->setContent('Description de l\'essai')
        
        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::NOT_BLANK_CONSTRAINT_TITLE_MESSAGE, $errors[0]->getMessage());
    }

    public function testTaskEntityIsInvalidNoContentEntered(): void 
    {
        $user = new User();
        $user
            ->setTitle('Un essai')
        
        $errors = $this->getValidationErrors($user, 1);

        $this->assertEquals(self::NOT_BLANK_CONSTRAINT_CONTENT_MESSAGE, $errors[0]->getMessage());
    }

    private function getValidationErrors(Task $task, int $numberExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($task);

        $this->assertCount($numberExpectedErrors, $errors);

        return $errors;
    }
}