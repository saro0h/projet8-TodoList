<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['TRICK_DELETE'])
            && $subject instanceof Trick;
    }


    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Trick $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if the user is admin, always return true
        if (false !== array_search('ROLE_ADMIN', $user->getRoles())){
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TRICK_DELETE':
                // only the trick owner can delete it
                if ($subject->getUser() === $user){
                    return true;
                }
                break;
        }

        return false;
    }
}
