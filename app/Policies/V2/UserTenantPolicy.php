<?php

namespace App\Policies\V2;


use App\Models\User;
use App\Models\UserTenant;

/**
 * Class UserTenantPolicy
 * @package App\Policies\V2
 */
class UserTenantPolicy extends \App\Policies\UserTenantPolicy
{
    public function manageUsers(User $user)
    {
        return $user->able('ACP_ACCESS');
    }

    public function updatePermissions(User $user, UserTenant $userTenant)
    {
        return $user->able('EDIT_TENANT');
    }
}