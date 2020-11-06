<?php

namespace App\Repositories;

use App\Models\UserTenantTask;

/**
 * Class UserTenantTaskRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property UserTenantTask $model
 */
class UserTenantTaskRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenantTask::class;
    }

    /**
     * @param int $taskId
     * @param int $userTenantId
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function attachUserTenantToTask(int $taskId, int $userTenantId)
    {
        return $this->model->firstOrCreate([
            'task_id'           => $taskId,
            'user_tenant_id'    => $userTenantId
        ]);
    }

    /**
     * @param int $taskId
     * @param int $userTenantId
     *
     * @return bool|null
     * @throws \Exception
     */
    public function detachUserTenantToTask(int $taskId, int $userTenantId)
    {
        if ($model = $this->model->where('task_id', $taskId)->where('user_tenant_id', $userTenantId)->first()) {
            $model->delete();
        }

        return null;
    }


    /**
     * @param array $taskIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeByTaskIds(array $taskIds)
    {
        return $this->model->withoutGlobalScopes()->whereIn('task_id', $taskIds)->delete();
    }

    /**
     * @param array $taskIds
     * @param array $userTenantIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function detachUserTenantFromTasks(array $taskIds, array $userTenantIds)
    {
        return $this->model
            ->whereIn('task_id', $taskIds)
            ->whereIn('user_tenant_id', $userTenantIds)
            ->delete();
    }
}
