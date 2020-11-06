<?php

namespace App\Services\Permission;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Support\Collection;

class PermissionService
{
    /** @var \App\Repositories\PermissionRepositoryEloquent|\Illuminate\Foundation\Application|mixed */
    private $permissionRepo;

    /**
     * PermissionService constructor.
     */
    public function __construct()
    {
        $this->permissionRepo = app('PermissionRepo');
    }

    /**
     * @param array $permissionNames
     *
     * @return Collection
     */
    public function getPermissionsByNames(array $permissionNames) : Collection
    {
        return $this->permissionRepo->findWhereIn('name', $permissionNames);
    }

    public function getPermissions()
    {
        return app('PermissionRepo')->getPermissions();
    }

    public function getAllUserPermissions(User $user, UserTenant $userTenant)
    {
        /** @var Collection $globalPermissions */
        $globalPermissions = app('PermissionSer')->getPermissionsByUserId($user->id);
        $groupsPermissions = collect([]);
        foreach ($userTenant->groups()->withPivot('id')->get() as $group) {
            $groupsPermissions = $groupsPermissions->merge(app('PermissionSer')->getUserTenantPermissionsInGroup($group->pivot->id));
        }

        $hasTimeTrackingGlobalPermission = $globalPermissions->filter(function($permission) {
            return $permission->name === Permission::TIME_TRACKING_PERMISSION['name'];
        })->count();
        if(!$hasTimeTrackingGlobalPermission) {
            $groupsPermissions = $groupsPermissions->filter(function($permission) {
                return $permission->name !== Permission::TIME_TRACKING_PERMISSION['name'];
            });
        }

        return $globalPermissions->merge($groupsPermissions)->unique()->values();
    }

    public function getPermissionsByUserId(int $userId)
    {
        return app('PermissionRepo')->getPermissionsByUserId($userId);
    }

    public function getUserTenantPermissionsInGroup(int $userTenantGroupId)
    {
        return app('UserTenantGroupRepo')->getUserTenantPermissionsInGroup($userTenantGroupId);
    }

    public function getAvailablePermissions()
    {
        return app('RoleRepo')->findWhere(['name' => Role::CUSTOM_ROLE['name']])->first()->perms()->get(
            [
                'id as permissionId',
                'name as permissionName',
                'display_name as permissionDisplayName',
                'description as permissionDescription'
            ]
        );
    }
}