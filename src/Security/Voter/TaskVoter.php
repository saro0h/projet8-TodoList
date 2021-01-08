<?php


namespace App\Security\Voter;


use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['TASK_DELETE'])
            && $subject instanceof Task;
    }


    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        switch ($attribute) {
            case 'TASK_DELETE':
                // only the task owner can delete it
                if ($subject->getUser() === $user){
                    return true;
                }
                break;
        }

        return false;
    }
}