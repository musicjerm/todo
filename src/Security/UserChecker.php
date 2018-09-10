<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\AccountDisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param UserInterface|User $user
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // make sure user-account is not disabled
        if ($user->getIsActive() === false){
            throw new AccountDisabledException('Account is inactive');
        }
    }

    public function checkPreAuth(UserInterface $user): void
    {
        // place any pre-authorization checks here
    }
}