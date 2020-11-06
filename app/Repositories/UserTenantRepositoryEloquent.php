<?php

namespace App\Repositories;

use App\Models\PersonalDeadline;
use App\Models\Task;
use App\Models\UserTask;
use App\Models\UserTenant;
use App\Models\UserTenantTask;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserTenantRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenant::class;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    private function defaultBuilder()
    {
        return DB::table($this->userTenantTable)
            ->select([
                $this->userTenantTable.'.id             as id',
                $this->userTenantTable.'.is_owner       as is_owner',
                $this->userTenantTable.'.tenant_id      as tenant_id',
                $this->userTenantTable.'.user_id        as user_id',
                $this->userTenantTable.'.company_name   as company_name',
                $this->tenantTable.'.company_name       as tenant_company_name',
                $this->tenantTable.'.project_name       as tenant_project_name',
            ])
            ->leftJoin($this->tenantTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->tenantTable.'.id', $this->userTenantTable.'.tenant_id');
            });
    }

    /**
     * @param string $roleName
     * @param int    $userTenantId
     *
     * @return mixed
     */
    public function getManualCustomRole(string $roleName, int $userTenantId)
    {
        return $this->model->withoutGlobalScope('invited_user')->find($userTenantId)->tenant->customRoles()
            ->where('name', 'like', '%' .$roleName)
            ->where('is_manual', 1)
            ->pluck('name')
            ->first();
    }

    /**
     * @param int $tenantId
     *
     * @return Collection
     */
    public function getUserTenantsByTenantId(int $tenantId)
    {
        return $this->defaultBuilder()
            ->where($this->userTenantTable.'.tenant_id', $tenantId)
            ->get();
    }

    /**
     * @param int $tenantId
     * @param int $userId
     *
     * @return Collection
     */
    public function getUserTenantsByTenantIdAndUserId(int $tenantId, int $userId)
    {
        return $this->defaultBuilder()
            ->where($this->userTenantTable.'.tenant_id', $tenantId)
            ->where($this->userTenantTable.'.user_id', $userId)
            ->get();
    }

    /**
     * @param int $userTenantId
     *
     * @return Collection
     */
    public function getUserTenantsById(int $userTenantId)
    {
        return $this->defaultBuilder()
            ->where($this->userTenantTable.'.id', $userTenantId)
            ->get();
    }

    /**
     * @param array $userTenantIds
     *
     * @return Collection
     */
    public function getUserTenantsByIds(array $userTenantIds)
    {
        return $this->defaultBuilder()
            ->whereIn($this->userTenantTable.'.id', $userTenantIds)
            ->get();
    }

    /**
     * @param int $tenantId
     * @param array $userIds
     *
     * @return Collection
     */
    public function getUserTenantsByTenantIdAndUserIds(int $tenantId, array $userIds)
    {
        return $this->defaultBuilder()
            ->where($this->userTenantTable.'.tenant_id', $tenantId)
            ->whereIn($this->userTenantTable.'.user_id', $userIds)
            ->get();
    }

    /**
     * @param array $groupIds
     *
     * @return Collection
     */
    public function getUserTenantsByGroupIds(array $groupIds)
    {
        return $this->defaultBuilder()
            ->addSelect([
                $this->groupTable.'.id as group_id',
            ])
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->userTenantGroupTable.'.user_tenant_id', $this->userTenantTable.'.id');
            })
            ->join($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id', $this->userTenantGroupTable.'.group_id');
            })
            ->join($this->userTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTable.'.id', $this->userTenantTable.'.user_id');
            })
            ->leftJoin($this->userProfileTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userProfileTable.'.user_id', $this->userTable.'.id');
            })
            ->leftJoin($this->imageTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->imageTable.'.id', $this->userProfileTable.'.avatar_id');
            })
            ->where($this->userTable.'.is_confirmed', 1)
            ->whereIn($this->groupTable.'.id', $groupIds)
            ->get();
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getTaskSubscribersByTaskIds(array $taskIds)
    {
        return $this->defaultBuilder()
            ->addSelect([
                $this->userTenantTaskTable.'.task_id as task_id',
            ])
            ->join($this->userTenantTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTaskTable.'.user_tenant_id', $this->userTenantTable.'.id');
            })
            ->whereIn($this->userTenantTaskTable.'.task_id', $taskIds)
            ->get();
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getNotifySubscribersByTaskIds(array $taskIds)
    {
        return $this->defaultBuilder()
            ->addSelect([
                $this->notificationSubscriptionTable.'.task_id as task_id',
            ])
            ->join($this->notificationSubscriptionTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->notificationSubscriptionTable.'.user_id', $this->userTenantTable.'.user_id');
            })
            ->whereIn($this->notificationSubscriptionTable.'.task_id', $taskIds)
            ->get();
    }
}
