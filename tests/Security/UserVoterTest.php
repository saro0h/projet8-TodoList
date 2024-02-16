<?php

namespace App\Tests\Security;

use App\Security\UserVoter;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;

class UserVoterTest extends \PHPUnit\Framework\TestCase
{
    private $voter;

    public function setUp(): void
    {
        $this->voter = new UserVoter();
    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, ?User $userSubject, ?User $user, $expected)
    {
        $token = new AnonymousToken('secret', 'anonymous');
        if ($user) {
            $token = new UsernamePasswordToken(
                $user, 'password', 'memory'
            );
        }

        $this->assertSame($expected, $this->voter->vote($token, $userSubject, [$attribute]));
    }

    public function provideCases()
    {
        return [
            [
                'false',
                new User(),
                null,
                0
            ],
            [
                'view',
                null,
                null,
                0
            ],
            [
                'view',
                new User(),
                null,
                UserVoter::ACCESS_DENIED
            ],
            [
                'view',
                new User(),
                $this->createUser('simon'),
                UserVoter::ACCESS_DENIED
            ],
            [
                'view',
                new User(),
                $this->createUser('simon', ['ROLE_USER', 'ROLE_ADMIN']),
                UserVoter::ACCESS_GRANTED
            ],
            [
                'add',
                new User(),
                null,
                UserVoter::ACCESS_DENIED
            ],
            [
                'add',
                new User(),
                $this->createUser('simon'),
                UserVoter::ACCESS_DENIED
            ],
            [
                'add',
                new User(),
                $this->createUser('simon', ['ROLE_USER', 'ROLE_ADMIN']),
                UserVoter::ACCESS_GRANTED
            ],
            [
                'edit',
                new User(),
                null,
                UserVoter::ACCESS_DENIED
            ],
            [
                'edit',
                new User(),
                $this->createUser('simon'),
                UserVoter::ACCESS_DENIED
            ],
            [
                'edit',
                new User(),
                $this->createUser('simon', ['ROLE_USER', 'ROLE_ADMIN']),
                UserVoter::ACCESS_GRANTED
            ]
        ];
    }

    private function createUser(string $username, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);

        return $user;
    }
}
