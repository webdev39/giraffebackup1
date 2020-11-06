<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class RoleRepositoryEloquent extends BaseRepositoryEloquent
{
    /** Table names list */
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
        return Role::class;
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
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function buildRoleWithRelations()
    {
        return DB::table($this->roleTable)
            ->select([
                $this->roleTable.'.id',
                $this->roleTable.'.name',
                $this->roleTable.'.display_name',
                $this->roleTable.'.description',
                $this->roleTable.'.is_default',
                $this->roleTable.'.is_manual',
            ]);
    }

    /**
     * @param array $userTenantIds
     *
     * @return mixed
     */
    public function getRolesByUserTenantIds(array $userTenantIds)
    {
        return $this->buildRoleWithRelations()
            ->addSelect([
                $this->userTenantRoleTable.'.user_tenant_id',
                $this->userTenantRoleTable.'.can_invited'
            ])
            ->join($this->userTenantRoleTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantRoleTable.'.role_id', $this->roleTable.'.id');
            })
            ->whereIn($this->userTenantRoleTable.'.user_tenant_id', $userTenantIds)
            ->get();
    }

    /**
     * @param $roles
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableRoles($roles)
    {
        return $this->buildRoleWithRelations()
            ->whereIn('name', $roles)
            ->get();
    }

    /**
     * @param string $roleName
     * @param int    $userTenantId
     *
     * @return mixed
     */
    public function getRoleIdByName(string $roleName, int $userTenantId)
    {
        $role = $this->buildRoleWithRelations()
            ->where($this->roleTable.'.name', $roleName)
            ->first();

        return optional($role)->id;
    }
}
