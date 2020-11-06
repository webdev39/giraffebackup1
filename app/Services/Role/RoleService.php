<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\UserTenant;
use App\Repositories\RoleRepositoryEloquent;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class RoleService extends BaseService
{
    /**
     * @var RoleRepositoryEloquent
     */
    public $roleRepo;

    /**
     * GroupService constructor.
     */
    public function __construct()
    {
        $this->roleRepo  = app('RoleRepo');
    }

    /**
     * @param int $roleId
     *
     * @return mixed
     */
    public function getCustomRoleById(int $roleId)
    {
        return $this->roleRepo->with('perms')->findOrFail($roleId);
    }

    /**
     * @param array $userTenantIds
     *
     * @return mixed
     */
    public function getRolesByUserTenantIds(array $userTenantIds)
    {
        return $this->roleRepo->getRolesByUserTenantIds($userTenantIds);
    }

    /**
     * @param int $roleId
     *
     * @return bool
     */
    public function deleteRole(int $roleId): bool
    {
        return $this->roleRepo->delete($roleId);
    }

    /**
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getCustomRolesByTenantId(int $tenantId)
    {
        $roleIds = app('TenantCustomRoleRepo')->getCustomRoleIdsByTenantId($tenantId);

        return $this->roleRepo->with('perms')->findWhereIn('id', $roleIds);
    }

    /**
     * @param int    $tenantId
     * @param string $name
     *
     * @return bool
     */
    public function existNameCustomRolesByTenantId(int $tenantId, string $name) : bool
    {
        $roleIds = app('TenantCustomRoleRepo')->getCustomRoleIdsByTenantId($tenantId);

        return (bool) $this->roleRepo->findWhereIn('id', $roleIds)->where('display_name', $name)->count();
    }

    /**
     * @return mixed
     */
    public function getRolesForDefaultGroupMembers()
    {
       return $this->roleRepo->findWhereIn('name', Role::AVAILABLE_GROUP_LEVEL_ROLES)->where('is_default', 1);
    }

    /**
     * @param array      $attributes
     * @param Collection $permissions
     * @param int        $tenantId
     * @param int        $isManual
     *
     * @return Role
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createCustomRole(array $attributes, Collection $permissions, int $tenantId, $isManual = 0)
    {
        $attributes['name']      = self::getGenerateRoleName($attributes['display_name']);
        $attributes['is_manual'] = $isManual;

        /** @var Role $role */
        $role = $this->roleRepo->create($attributes);

        app('TenantCustomRoleRepo')->create(['role_id' => $role->id, 'tenant_id' => $tenantId]);

        if ($permissions) {
            $role->attachPermissions($permissions);
        }

        return $role;
    }

    /**
     * @param array      $attributes
     * @param Collection $permissions
     * @param int        $roleId
     *
     * @return Role
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCustomRole(array $attributes, Collection $permissions, int $roleId)
    {
        $attributes['name'] = self::getGenerateRoleName($attributes['display_name']);

        /** @var Role $role */
        $role = $this->roleRepo->update($attributes, $roleId);

        if (count($role->perms)) {
            $role->detachPermissions($role->perms);
        }

        if ($permissions) {
            $role->attachPermissions($permissions);
        }

        return $role;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function getGenerateRoleName($name) : string
    {
        return str_random(8).'_'.strtolower(str_replace(' ', '-', trim($name)));
    }

    /**
     * @param int  $memberId
     * @param int  $tenantId
     * @param Role $role
     */
    public function attachRoleToMember(int $memberId, int $tenantId, Role $role)
    {
        /** @var UserTenant $userTenant */
        $userTenant = app('UserTenantRepo')->findWhere(['user_id' => $memberId, 'tenant_id' => $tenantId])->first();
        $userTenant->roles()->sync([$role->id]);
    }

    /**
     * @param int  $memberId
     * @param int  $tenantId
     * @param Role $role
     */
    public function detachRoleFromMember(int $memberId, int $tenantId, Role $role)
    {
        /** @var UserTenant $userTenant */
        $userTenant = app('UserTenantRepo')->findWhere(['user_id' => $memberId, 'tenant_id' => $tenantId])->first();
        $userTenant->detachRole($role);
    }
}
