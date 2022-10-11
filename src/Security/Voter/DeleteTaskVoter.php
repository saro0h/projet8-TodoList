<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DeleteTaskVoter extends Voter
{
    public const DELETE_TASK = 'delete_task';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $task): bool
    {
        return in_array($attribute, [self::DELETE_TASK])
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        return $this->canDelete($task, $user);
    }

    private function canDelete(Task $task, UserInterface $user): bool
    {
        if ($task->getAuthor()->getUsername() === 'anonyme' && $this->security->isGranted('ROLE_ADMIN')) return true;
        return $user === $task->getAuthor();
    }
}
