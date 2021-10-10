<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Task;

class TaskVoter extends Voter
{
    const DELETE_TASK = 'delete_task';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, ['delete_task']) && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE_TASK:
                return $this->canDelete($subject, $user);
        }
        return false;
    }

    private function canDelete($task, $user): bool
    {
        if (($task->getAuthor() == 'anonymous')
            && (
            $this->security->isGranted('ROLE_ADMIN')) ||
            ($task->getAuthor() == $user
            )) {
            return true;
        }
            return false;
    }
}
