<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
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

    public function hasTenant(User $user, Tenant $tenant)
    {
        $userTenant = app('UserTenantRepo')->findWhere(
            [
                'user_id'     => $user->id,
                'tenant_id'   => $tenant->id,
                'invite_hash' => null
            ]
        )->first();
        return (bool) $userTenant;
    }

    public function isMember(User $user, Tenant $tenant, User $member)
    {
        $userTenant = app('UserTenantRepo')->findWhere(
            [
                'user_id'     => $member->id,
                'tenant_id'   => $tenant->id,
                'is_owner'    => 0,
                'invite_hash' => null
            ]
        )->first();
        return (bool) $userTenant;
    }

    public function update(User $user, Tenant $tenant)
    {
        $userTenant = app('UserTenantRepo')->findWhere(
            [
                'user_id'     => $user->id,
                'tenant_id'   => $tenant->id,
                'is_owner'    => 1,
                'invite_hash' => null
            ]
        )->first();
        return (bool) $userTenant;
    }
}
