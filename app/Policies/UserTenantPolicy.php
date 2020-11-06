<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserTenantPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function isOwnerOfTenant(User $user, UserTenant $userTenant)
    {
        return (bool) app('UserTenantRepo')->findWhere([
            'user_id'   => $user->id,
            'tenant_id' => $userTenant->tenant_id,
            'is_owner'  => 1
        ])->first();
    }

    public function update(User $user, UserTenant $userTenant)
    {
        $userTenantOwner = $user->getChosenTenant();
        return $userTenantOwner->tenant_id === $userTenant->tenant_id;
    }

    /**
     * Checking of opportunity of the user to read billing reports
     *
     * @param User $user
     * @param UserTenant $userTenant
     *
     * @return bool
     */
    public function readBillingReports(User $user, UserTenant $userTenant) :bool
    {
        Auth::failIfHasNoPermission(Permission::READ_BILLING_PERMISSION);
        return true;
    }
}
