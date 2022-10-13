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
    private function createUser(int $id): User
    {
        $user = $this->createMock(User::class);
        $user->setId($id);

        return $user;
    }

    public function provideCases()
    {
        yield 'anonymous cannot delete' => [
            ['DELETE'],
            new Task($this->createUser(1)),
            null,
            VoterInterface::ACCESS_DENIED
        ];

        yield 'non-owner cannot delete' => [
            ['DELETE'],
            new Task($this->createUser(1)),
            $this->createUser(2),
            VoterInterface::ACCESS_DENIED
        ];

        yield 'owner can delete' => [
            ['DELETE'],
            new Task($this->createUser(1)),
            $this->createUser(1),
            VoterInterface::ACCESS_GRANTED
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(
        string $attribute,
        Task $task,
        ?User $user,
        $expectedVote
    ) {
        $voter = new TaskVoter();

        $token = new AnonymousToken('secret', 'anonymous');
        if ($user) {
            $token = new UsernamePasswordToken(
                $user, 'credentials', 'memory'
            );
        }

        $this->assertSame(
            $expectedVote,
            $voter->vote($token, $task, [$attribute])
        );
    }








//     /**
//      * @dataProvider provideCases
//      */
//     public function testVote(array $attributes,
//  string $subject, 
//  $expectedVote) {
//         $this->assertEquals($expectedVote, $this->voter->vote($this->token, $subject, $attributes));
//     }
    
    
// public function provideCases(): \Generator
// {

//     yield 'user can delete' => [
//         ['DELETE'],
//         'my_subject',
//         $this->token,
//         VoterInterface::ACCESS_GRANTED,
//     ];
// }
    
    


    //$this->assertTrue($test);
    // public function testVoteOnAttributeFalse()
    // {
    //     $tokenInterface = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
    //     $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
    //     $voter = new TaskVoter($security);
    //     $this->assertEquals(0, $voter->vote($tokenInterface, (new Task()), ["WRONG_ATTRIBUTE"]));
    // }
}