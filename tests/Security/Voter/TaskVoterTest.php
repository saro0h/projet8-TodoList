<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\TaskVoter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
// use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskVoterTest extends TestCase
{
    private $task;

    private $voter;

    public function setUp(): void
    {
        $this->task = new Task();
        $this->voter = new TaskVoter();
    }
    
    
    
    private function createUser(int $id): User
    {
        $user = $this->createMock(User::class);
        $user->setId($id);

        return $user;
    }

    private function createTask($user = null): Task
    {
        $task = new Task();
        $task->setUser($user);

        return $task;
    }
    private function createUserRoles(int $id, string $roles): User
    {
        $user = new User();
        $user->setId($id);
        $user->setRoles([$roles]);

        return $user;
    }

    public function provideCases(): ?\Generator
    {
        // yield 'anonymous cannot delete' => [
        //     ['DELETE'],
        //     new Task($this->createUser(1)),
        //     null,
        //     VoterInterface::ACCESS_DENIED
        // ];

        // yield 'non-owner cannot delete' => [
        //     ['DELETE'],
        //     new Task($this->createUser(1)),
        //     $this->createUser(2),
        //     VoterInterface::ACCESS_DENIED
        // ];

        // yield 'owner can delete' => [
        //     ['DELETE'],
        //     new Task($this->createUser(1)),
        //     $this->createUser(1),
        //     VoterInterface::ACCESS_GRANTED
        // ];

        yield 'Admin peut supprimer une tache Anonyme' => [
            $this->createUserRoles(2, 'ROLE_ADMIN'),
            $task = $this->createTask(Null),
            $attribute = ['DELETE'],
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'User peut supprimer sa propre tâche' => [
            $user = $this->createUser(2),
            $task = $this->createTask($user),
            $attribute = ['DELETE'],
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'User ne peut pas supprimer une tâche de un autre user ' => [
            $this->createUser(2),
            $task = $this->createTask($this->createUser(4)),
            $attribute = ['DELETE'],
            TaskVoter::ACCESS_DENIED,
        ];
    }
 /**
     * @dataProvider provideCases
     */
    public function testSupportsFalse()
    {
        $tokenInterface = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $voter = new TaskVoter($security);
        $this->assertEquals(0, $voter->vote($tokenInterface, (new Task()), ["WRONG_ATTRIBUTE"]));
    }

    /**
     * @dataProvider provideCases
     */
    public function testSupportsTrue()
    {
        $tokenInterface = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $voter = new TaskVoter($security);
        $this->assertEquals(-1, $voter->vote($tokenInterface, (new Task()), ["DELETE"]));
    }

    public function testVoteOnAttributeTrue()
    {
        $tokenInterface = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $voter = new TaskVoter($security);
        $user=$tokenInterface->getUser();
        $task=new Task();
        $user=$task->getUser();
        $this->assertEquals(-1, $voter->vote($tokenInterface, $task, ["DELETE"]));
    }
}