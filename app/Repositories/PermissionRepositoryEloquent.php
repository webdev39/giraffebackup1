<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class PermissionRepositoryEloquent extends BaseRepositoryEloquent
{
    /** Table names list */
    public $permissionTable;
    public $roleTable;
    public $rolePermissionTable;
    public $userTenantRoleTable;
    
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
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
        $this->userTenantRoleTable          = config('custom_entrust.entities.'.Role::USER_TENANT_ENTITY.'.table');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        return DB::table($this->permissionTable)
            ->select([
                $this->permissionTable . '.id',
                $this->permissionTable . '.name',
                $this->permissionTable . '.display_name',
                $this->permissionTable . '.description'
            ])
            ->get();
    }

    /**
     * @param int $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsByUserId(int $userId)
    {
        return DB::table($this->permissionTable)
            ->select([
                $this->permissionTable.'.id',
                $this->permissionTable.'.name',
                $this->permissionTable.'.display_name',
                $this->permissionTable.'.description'
            ])
            ->join($this->rolePermissionTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->rolePermissionTable.'.permission_id', $this->permissionTable.'.id');
            })
            ->join($this->roleTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->roleTable.'.id', $this->rolePermissionTable.'.role_id');
            })
            ->join($this->userTenantRoleTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantRoleTable.'.role_id', $this->roleTable.'.id');
            })
            ->join($this->userTenantTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTable.'.id', $this->userTenantRoleTable.'.user_tenant_id');
            })
            ->join($this->userTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTable.'.id', $this->userTenantTable.'.user_id');
            })
            ->whereRaw($this->userTenantTable.'.tenant_id = '.$this->userTable.'.chosen_tenant_id')
            ->where($this->userTable.'.id', $userId)
            ->get();
    }
}
