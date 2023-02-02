<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskVoterTest extends WebTestCase
{
    private function createUser(): User
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    private function createTask(?User $user = null): Task
    {
        $task = new Task();
        $task->setUser($user);

        return $task;
    }

    public function provideCases(): \Generator
    {
        yield 'Utilisateur peut supprimer sa tâche' => [
            $user = $this->createUser(),
            $task = $this->createTask($user),
            $attribute = 'TASK_DELETE',
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'Utilisateur ne peut supprimer la tâche ne lui appartenant pas' => [
            $this->createUser(),
            $task = $this->createTask($this->createUser()),
            $attribute = 'TASK_DELETE',
            TaskVoter::ACCESS_DENIED,
        ];

        yield 'Utilisateur annonyme ne peut pas supprimer de tâche' => [
            $user = null,
            $task = $this->createTask(),
            $attribute = 'TASK_DELETE',
            TaskVoter::ACCESS_DENIED,
        ];

        yield 'Sujet inexistant' => [
            $user = null,
            $task = $this->createTask(null),
            $attribute = 'TASK_OTHER',
            TaskVoter::ACCESS_ABSTAIN,
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(
        ?User $user,
        Task $task,
        string $attribute,
        int $expectedVote): void
    {
        $voter = new TaskVoter();

        $token = new NullToken();
        if (null !== $user) {
            $token = new UsernamePasswordToken(
                $user, 'password', []
            );
        }

        self::assertSame($expectedVote, $voter->vote($token, $task, [$attribute]));
    }
}
