<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 26.06.17
 * Time: 17:13
 */

namespace App\Repositories;

use App\Models\TenantCustomRole;
use Illuminate\Database\Query\JoinClause;

/**
 * Class TenantCustomRoleRepositoryEloquent
 * @package App\Repositories
 * @property $model TenantCustomRole
 */
class TenantCustomRoleRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TenantCustomRole::class;
    }

    /**
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getCustomRoleIdsByTenantId(int $tenantId)
    {
        return $this->model->where('tenant_id', $tenantId)->pluck('role_id')->toArray();
    }
}
