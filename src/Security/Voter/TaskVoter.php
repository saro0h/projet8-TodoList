<?php


namespace App\Security\Voter;


use App\Entity\Task;
use App\Entity\User;
use App\Service\ManageUsers;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    /**
     * @var ManageUsers
     */
    private $manageUsers;


    /**
     * TaskVoter constructor.
     * @param ManageUsers $manageUsers
     */
    public function __construct(ManageUsers $manageUsers)
    {
        $this->manageUsers = $manageUsers;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['TASK_DELETE'])
            && $subject instanceof Task;
    }


    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        switch ($attribute) {
            case 'TASK_DELETE':
                // only the task owner can delete it, or admin if task owner is "anonymous"
                if ($subject->getUser() === $user || ($this->manageUsers->isAdmin($user) && $this->manageUsers->isAnonymous($subject->getUser()))) {
                    return true;
                }
                break;
        }

        return false;
    }
}