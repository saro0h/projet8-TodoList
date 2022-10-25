<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    const DELETE = 'delete';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        // only vote on `Task` objects
        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Task object, thanks to `supports()`
        /** @var Task $task */
        $task = $subject;

        return match ($attribute) {
            self::DELETE => $this->canDelete($task, $user),
            default => throw new \LogicException('Vous ne pouvez pas supprimer cette tâche.')
        };
    }

    private function canDelete(Task $task, User $user): bool
    {
        // check that the connected user is the same as the user that created the task
        // and if the user is an admin
        if ($user === $task->getUser() or $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
