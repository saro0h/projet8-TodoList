<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class TaskVoterTest extends WebTestCase
{
    private function createUser(int $id): User
    {
        $user = new User();
        $user->setId($id);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    private function createTask($user = null): Task
    {
        $task = new Task();
        $task->setUser($user);

        return $task;
    }

    public function provideCases(): ?\Generator
    {
        yield 'Utilisateur peut supprimer sa tâche' => [
            $user = $this->createUser(2),
            $task = $this->createTask($user),
            $attribute = 'TASK_DELETE',
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'Utilisateur ne peut supprimer la tâche ne lui appartenant pas' => [
            $this->createUser(2),
            $task = $this->createTask($this->createUser(4)),
            $attribute = 'TASK_DELETE',
            TaskVoter::ACCESS_DENIED,
        ];

        yield 'Utilisateur annonyme ne peut pas supprimer de tâche' => [
            $user = null,
            $task = $this->createTask(null),
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
     * @param $user
     * @param Task $task
     * @param string $attribute
     * @param int $expectedVote
     */
    public function testVote(
        $user,
        Task $task,
        string $attribute,
        int $expectedVote): void
    {

        $voter = new TaskVoter();

        $token = new AnonymousToken('secret', 'anonymous');
        if ($user) {
            $token = new UsernamePasswordToken(
                $user, 'password', 'memory'
            );
        }

        $this->assertSame($expectedVote, $voter->vote($token, $task, [$attribute]));
    }
}