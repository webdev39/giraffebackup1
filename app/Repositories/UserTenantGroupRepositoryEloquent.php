<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\UserTenantGroup;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class UserTenantGroupRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Table names list
     */
    public $permissionTable;
    public $roleTable;
    public $rolePermissionTable;
    public $userTenantGroupTable;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenantGroup::class;
    }

    /**
     * UserTenantRepositoryEloquent constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->roleTable                    = config('custom_entrust.roles_table');
        $this->rolePermissionTable          = config('custom_entrust.permission_role_table');
        $this->permissionTable              = config('custom_entrust.permissions_table');
        $this->userTenantGroupRoleTable     = config('custom_entrust.entities.'.Role::USER_TENANT_GROUP_ENTITY.'.table');
    }

    /**
     * @param array $userTenantIds
     *
     * @return Collection
     */
    public function getUserTenantGroupsByUserTenantIds(array $userTenantIds) : Collection
    {
        return DB::table($this->userTenantGroupTable)
            ->select([
                $this->userTenantGroupTable.'.id',
                $this->userTenantGroupTable.'.group_id',
                $this->userTenantGroupTable.'.is_creator',
                $this->userTenantGroupTable.'.user_tenant_id',
            ])
            ->whereIn($this->userTenantGroupTable.'.user_tenant_id', $userTenantIds)
            ->get();
    }

    /**
     * @param array $groupIds
     * @param null $userTenantId
     * @return Collection
     */
    public function getUserTenantGroupPermissionsByGroupIds(array $groupIds, $userTenantId = null) : Collection
    {
        $permissionsQuery = DB::table($this->permissionTable)
            ->select([
                $this->permissionTable.'.id             as id',
                $this->permissionTable.'.name           as name',
                $this->permissionTable.'.display_name   as display_name',
                $this->permissionTable.'.description    as description',
                $this->userTenantGroupTable.'.group_id  as group_id',
                $this->userTenantGroupTable . '.user_tenant_id'
            ])
            ->join($this->rolePermissionTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->rolePermissionTable.'.permission_id', $this->permissionTable.'.id');
            })
            ->join($this->roleTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->roleTable.'.id', $this->rolePermissionTable.'.role_id');
            })
            ->join($this->userTenantGroupRoleTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantGroupRoleTable.'.role_id', $this->roleTable.'.id');
            })
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantGroupTable.'.id', $this->userTenantGroupRoleTable.'.user_tenant_group_id');
            })
            ->whereIn($this->userTenantGroupTable.'.group_id', $groupIds);
            if(!is_null($userTenantId)) {
                $permissionsQuery->where($this->userTenantGroupTable . '.user_tenant_id', $userTenantId);
            }

            $permissions = $permissionsQuery->distinct()->get();

            return $permissions;
    }

    /**
     * @param int $userTenantGroupId
     *
     * @return Collection
     */
    public function getUserTenantPermissionsInGroup(int $userTenantGroupId)
    {
        return DB::table($this->userTenantGroupTable)
            ->select([
                $this->permissionTable.'.id',
                $this->permissionTable.'.name',
                $this->permissionTable.'.display_name',
                $this->permissionTable.'.description'
            ])
            ->join($this->userTenantGroupRoleTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupRoleTable.'.user_tenant_group_id', $this->userTenantGroupTable.'.id');
            })
            ->join($this->roleTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->roleTable.'.id', $this->userTenantGroupRoleTable.'.role_id');
            })
            ->join($this->rolePermissionTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->rolePermissionTable.'.role_id', $this->roleTable.'.id');
            })
            ->join($this->permissionTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->permissionTable.'.id', $this->rolePermissionTable.'.permission_id');
            })
            ->where($this->userTenantGroupTable.'.id', $userTenantGroupId)
            ->get();
    }

    /**
     * @param array $userTenantGroupIds
     *
     * @return Collection
     */
    public function getUserTenantGroupRolesByIds(array $userTenantGroupIds)
    {
        return DB::table($this->userTenantGroupTable)
            ->select([
                $this->roleTable.'.id',
                $this->roleTable.'.name',
                $this->roleTable.'.display_name',
                $this->roleTable.'.description',
                $this->roleTable.'.is_default',
                $this->roleTable.'.is_manual',
                $this->userTenantGroupTable.'.id as user_tenant_group_id',
            ])
            ->join($this->userTenantGroupRoleTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupRoleTable.'.user_tenant_group_id', $this->userTenantGroupTable.'.id');
            })
            ->join($this->roleTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->roleTable.'.id', $this->userTenantGroupRoleTable.'.role_id');
            })
            ->whereIn($this->userTenantGroupTable.'.id', $userTenantGroupIds)
            ->get();
    }
}
