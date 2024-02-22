<?php

namespace App\Tests\Security;

use App\Security\TaskVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use App\Entity\User;
use App\Entity\Task;

class TaskVoterTest extends \PHPUnit\Framework\TestCase
{
    private $voter;

    private $lastUser;

    public function setUp(): void
    {
        $this->voter = new TaskVoter();
        $this->lastUser = null;
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, ?Task $task, ?User $user, $expected)
    {
        $token = $this->createMock(TokenInterface::class);
        if ($user) {
            $token->method('getUser')->willReturn($user);
        } else {
            $token->method('getUser')->willReturn(null);
        }

        $this->assertSame($expected, $this->voter->vote($token, $task, [$attribute]));
    }

    public function provideCases()
    {
        return [
            [
                'false',
                new Task(),
                null,
                0
            ],
            [
                'add',
                null,
                null,
                0
            ],
            [
                'add',
                new Task(),
                null,
                TaskVoter::ACCESS_DENIED
            ],
            [
                'add',
                new Task(),
                $this->createUser('simon'),
                TaskVoter::ACCESS_GRANTED
            ],
            [
                'edit',
                $this->createTask($this->createUser('simon')),
                null,
                TaskVoter::ACCESS_DENIED
            ],
            [
                'edit',
                $this->createTask($this->createUser('simon')),
                $this->createUser('john'),
                TaskVoter::ACCESS_DENIED
            ],
            [
                'edit',
                $this->createTask($this->createUser('simon')),
                $this->lastUser,
                TaskVoter::ACCESS_GRANTED
            ],
            [
                'edit',
                $this->createTask($this->createUser('simon')),
                $this->createUser('john', ['ROLE_USER', 'ROLE_ADMIN']),
                TaskVoter::ACCESS_DENIED
            ],
            [
                'edit',
                $this->createTask($this->createUser('anonyme')),
                $this->createUser('john', ['ROLE_USER', 'ROLE_ADMIN']),
                TaskVoter::ACCESS_GRANTED
            ],
            [
                'delete',
                $this->createTask($this->createUser('simon')),
                null,
                TaskVoter::ACCESS_DENIED
            ],
            [
                'delete',
                $this->createTask($this->createUser('simon')),
                $this->createUser('john'),
                TaskVoter::ACCESS_DENIED
            ],
            [
                'delete',
                $this->createTask($this->createUser('simon')),
                $this->lastUser,
                TaskVoter::ACCESS_GRANTED
            ],
            [
                'delete',
                $this->createTask($this->createUser('simon')),
                $this->createUser('john', ['ROLE_USER', 'ROLE_ADMIN']),
                TaskVoter::ACCESS_DENIED
            ],
            [
                'delete',
                $this->createTask($this->createUser('anonyme')),
                $this->createUser('john', ['ROLE_USER', 'ROLE_ADMIN']),
                TaskVoter::ACCESS_GRANTED
            ]
        ];
    }

    private function createUser(string $username, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);

        $this->lastUser = $user;

        return $user;
    }

    private function createTask(User $user): Task
    {
        $task = new Task();
        $task->setUser($user);

        return $task;
    }
}
