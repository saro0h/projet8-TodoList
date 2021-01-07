<?php


namespace App\Security;

use App\Entity\User;
use App\Service\ManageUsers;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{


    /** @var ManageUsers */
    private $manageUsers;

    public function __construct(ManageUsers $manageUsers)
    {
        $this->manageUsers = $manageUsers;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if ($this->manageUsers->getAnonymousUser() === $user) {
            throw new CustomUserMessageAccountStatusException('User \'anonymous\' is disabled and cannot log in');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }
}