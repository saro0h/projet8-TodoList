<?php

namespace App\DataFixtures\Providers;

use Faker\Provider\Base as BaseProvider;

final class RoleProvider extends BaseProvider
{

   public function userRole(): string
    {
        $roles = array("ROLE_USER","ROLE_ADMIN");
        return $roles[array_rand($roles,1)];
        
    }
}