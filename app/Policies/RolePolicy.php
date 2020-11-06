<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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

    public function update(User $user, Role $role)
    {
        return (bool) app('TenantCustomRoleRepo')->findWhere(
            [
                'tenant_id' => $user->chosen_tenant_id,
                'role_id' => $role->id
            ]
        )->first();
    }

    public function manageCustomRole(User $user, Role $role)
    {
        return (bool) app('TenantCustomRoleRepo')->findWhere(
            [
                'tenant_id' => $user->chosen_tenant_id,
                'role_id'   => $role->id
            ]
        );
    }
}
