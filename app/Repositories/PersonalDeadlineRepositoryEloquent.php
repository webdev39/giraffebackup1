<?php

namespace App\Repositories;

use App\Models\PersonalDeadline;

/**
 * Class PersonalDeadlineRepositoryEloquent
 * @package App\Repositories
 *
 * @property PersonalDeadline $model
 */
class PersonalDeadlineRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PersonalDeadline::class;
    }

    /**
     * @param string|null $deadline
     * @param int         $taskId
     * @param int         $userTenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreateDeadline($deadline, int $taskId, int $userTenantId)
    {
        return $this->updateOrCreate([
            'task_id'           => $taskId,
            'user_tenant_id'    => $userTenantId,
        ], [
            'planned_deadline'  => $deadline
        ]);
    }

    /**
     * @param int $taskId
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getPersonalDeadlineByTaskIds(int $taskId, int $userTenantId)
    {
        /** @var PersonalDeadline $model */
        $model = $this->findWhere([
            'user_tenant_id'    => $userTenantId,
            'task_id'           => $taskId
        ], ['planned_deadline'])->first();

        return optional($model)->planned_deadline;
    }

    /**
     * @param array $taskIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removePersonalDeadlineByTaskIds(array $taskIds)
    {
        return $this->model->withoutGlobalScopes()->whereIn('task_id', $taskIds)->delete();
    }

    public function removeDeadline(int $taskId, int $userTenantId)
    {
        return $this->model->where('task_id', $taskId)->where('user_tenant_id', $userTenantId)->delete();
    }
}
