<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class GroupPolicy
{
    use HandlesAuthorization;

    public function cloneGroup(User $user, Group $group)
    {
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);

        if (!$userTenantGroup) {
            abort(403, 'User has no permission to clone the group');
        }

        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::CREATE_GROUP_PERMISSION]
        );

        return true;
    }

    public function getAccess(User $user, Group $group)
    {
        $userTenant =  $user->getChosenTenant();

        return app('UserTenantGroupRepo')->findWhere(
            [
                'user_tenant_id' => $userTenant->id,
                'group_id'       => $group->id
            ]
        );
    }

    public function update(User $user, Group $group)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::UPDATE_GROUP_PERMISSION]
        );
        return true;
    }

    public function openGroupDetails(User $user, Group $group)
    {
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return abort(403, 'User is not member of the group');
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::READ_GROUP_PERMISSION]
        );
        return true;
    }
    public function updateGroupList(User $user, Group $group)
    {
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return abort(403, 'User is not member of the group');
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::UPDATE_GROUP_PERMISSION]
        );
        return true;
    }

    public function deleteGroupList(User $user, Group $group)
    {
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return abort(403, 'User is not member of the group');
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::DELETE_GROUP_PERMISSION]
        );
        return true;
    }

    public function manage(User $user, Group $group)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }

        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::MANAGE_GROUP_MEMBERS_PERMISSION]
        );
        return true;
    }


    public function createBoard(User $user, Group $group)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'You have no permissions'], 403);
        }

        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::CREATE_BOARD_PERMISSION]
        );
    }

    public function show(User $user, Group $group)
    {
        $userTenant = Auth::userTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::READ_GROUP_PERMISSION]);
    }

    public function destroy(User $user, Group $group)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::DELETE_GROUP_PERMISSION]
        );
    }

    public function attachRoleToGroupUser(User $user, Group $group, Role $role)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::MANAGE_GROUP_MEMBERS_PERMISSION]
        );
        if (in_array($role->name, Role::TENANT_LEVEL_ROLES_NAMES)) {
            return response()->json(['message' => 'You cant attach this role'], 422);
        }
        if (!Auth::user()->can('manageCustomRole', $role))
        {
            return response()->json(['message' => 'Tenant has no this role'], 404);
        }
    }

    public function detachRoleToGroupUser(User $user, Group $group, Role $role)
    {
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User Tenant is not member of the group'], 403);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::MANAGE_GROUP_MEMBERS_PERMISSION]
        );
        if (in_array($role->name, Role::TENANT_LEVEL_ROLES_NAMES)) {
            return response()->json(['message' => 'You cant detach this role'], 422);
        }
        if (!Auth::user()->can('manageCustomRole', $role))
        {
            return response()->json(['message' => 'Tenant has no this role'], 404);
        }
    }
}
