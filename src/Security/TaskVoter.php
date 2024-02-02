<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $task = $subject;

        return match($attribute) {
            self::ADD => $this->canAdd(),
            self::EDIT => $this->canEdit($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canAdd(): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }

    private function canEdit(Task $task, User $user): bool
    {
        return $user === $task->getUser() || ($this->security->isGranted('ROLE_ADMIN') && $task->getUser()->getUsername() === 'anonyme');
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $user === $task->getUser() || ($this->security->isGranted('ROLE_ADMIN') && $task->getUser()->getUsername() === 'anonyme');
    }
}
