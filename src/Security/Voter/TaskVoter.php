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
        // return in_array($attribute, [self::DELETE])
        //     && $subject instanceof \App\Entity\Task;
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }
    /** @var Task $task */
    protected function voteOnAttribute(
        string $attribute, 
        // mixed $task,
        $task, 
        TokenInterface $token
    ): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DELETE:
                
                if (($user->getRoles() === ["ROLE_ADMIN"]) && ($task->getUser() === null))  {
                    return true;
                }
                if ($user == $task->getUser()) {
                    return true;
                }
                break;
        }
        // throw new \LogicException('SUPPRESSION IMPOSSIBLE!');
        return false;
    }
}
