<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class GroupRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Group $model
 */
class GroupRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Group::class;
    }

    /**
     * @param bool|null $isArchived
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    private function buildGroupWithRelations(bool $isArchived = null)
    {
        $query = DB::table($this->groupTable)
            ->select([
                $this->groupTable.'.id',
                $this->groupTable.'.name',
                $this->groupTable.'.description',
                $this->groupTable.'.is_archive',
                $this->groupTable.'.creator_id',
                $this->userTenantGroupTable.'.id as userTenantGroupId'
            ])
            ->join($this->userTenantGroupTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantGroupTable.'.group_id', $this->groupTable.'.id');
            });

        if (!is_null($isArchived)) {
            $query->where($this->groupTable.'.is_archive', $isArchived);
        }

        return $query;
    }

    /**
     * @param int       $userTenantId
     * @param bool|null $isArchived
     *
     * @return Collection
     */
    public function getGroupsByUserTenantId(int $userTenantId, bool $isArchived = null) : Collection
    {
        return $this->buildGroupWithRelations($isArchived)
            ->where($this->userTenantGroupTable.'.user_tenant_id', $userTenantId)
            ->get()
            ->unique('id');
    }

    /**
     * @param int       $tenantId
     * @param bool|null $isArchived
     *
     * @return Collection
     */
    public function getGroupsByTenantId(int $tenantId, bool $isArchived = null) : Collection
    {
        return $this->buildGroupWithRelations($isArchived)
            ->where($this->groupTable.'.tenant_id', $tenantId)
            ->get()
            ->unique('id');
    }

    /**
     * @param bool|null $isArchived
     *
     * @return Collection
     */
    public function getGroups(bool $isArchived = null) : Collection
    {
        return $this->buildGroupWithRelations($isArchived)
            ->get()
            ->unique('id');
    }

    /**
     * @param array $groupIds
     *
     * @return Collection
     */
    public function getGroupsByIds(array $groupIds) : Collection
    {
        return $this->buildGroupWithRelations()
            ->whereIn($this->groupTable.'.id', $groupIds)
            ->get();
    }

    /**
     * @param array $groupIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveGroupByIds(array $groupIds, bool $isArchive = false)
    {
        DB::transaction(function () use ($groupIds, $isArchive) {
            $groups = $this->model->findMany($groupIds);

            $groups->each(function ($group) use ($isArchive) {
                /** @var Group $group */
                $group->is_archive = $isArchive ? 1 : 0;
                $group->save();
            });
        });
    }
}
