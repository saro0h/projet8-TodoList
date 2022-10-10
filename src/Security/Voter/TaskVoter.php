<?php

namespace App\Security\Voter;
use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::DELETE]) || (!$subject instanceof Task)) {
            return false;
        }

        return true;
    }
    /** @var Task $task */
    protected function voteOnAttribute(
        string $attribute, 
        $task, 
        TokenInterface $token
    ): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return ((($user->getRoles() === ["ROLE_ADMIN"]) && ($task->getUser() === null)) || ($user == $task->getUser()));
    }
}
