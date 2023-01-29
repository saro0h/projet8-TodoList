<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * TaskVoter class
 */
class TaskVoter extends Voter
{
    const AUTHORIZE = 'authorize';
    private Security $security;

    /**
     * TaskVoter constructor
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return boolean
     */
    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        // If the attribute isn't one we support, return false
        if (!in_array($attribute, [self::AUTHORIZE])) {
            return false;
        }

        // Only vote on `Task` objects
        if (!$subject instanceof Task) {
            return false; // @codeCoverageIgnore
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return boolean
     */
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // The user must be logged in; if not, deny access
            return false; // @codeCoverageIgnore
        }

        // You know $subject is a Task object, thanks to `supports()`
        /** @var Task $task */
        $task = $subject;

        return match ($attribute) {
            self::AUTHORIZE => $this->canHandle($task, $user),
            default => throw new \LogicException( // @codeCoverageIgnore
                'Vous n\'avez pas les droits.'
            )
        };
    }

    /* A task can be handled by their author or an admin
    and an anonyme task can be handle only by an admin */
    /**
     * @param Task $task
     * @param User $user
     * @return boolean
     */
    private function canHandle(Task $task, User $user): bool
    {
        return $user === $task->getUser() || $this->security->isGranted('ROLE_ADMIN');
    }
}
