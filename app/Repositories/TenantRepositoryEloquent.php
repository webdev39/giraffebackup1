<?php

namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TenantRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Tenant::class;
    }

    public function getAllTenants()
    {
        return DB::table($this->tenantTable)
            ->select([
                $this->tenantTable.'.id',
                $this->tenantTable.'.company_name',
                $this->tenantTable.'.project_name',
                $this->tenantTable.'.created_at',
                DB::raw('count('.$this->userTenantTable.'.id) as count_users')
            ])
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantTable.'.tenant_id',$this->tenantTable.'.id');
            })
            ->groupBy($this->tenantTable.'.id')
            ->get();
    }
}
