<?php

namespace App\Repositories;

use App\Models\UserTenantPriority;

class UserTenantPriorityRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenantPriority::class;
    }

    /**
     * @param int  $priorityId
     * @param int  $userTenantId
     * @param bool $isInvisible
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateInvisible(int $priorityId, int $userTenantId, bool $isInvisible)
    {
        return $this->updateOrCreate([
            'priority_id'    => $priorityId,
            'user_tenant_id' => $userTenantId,
        ],[
            'is_invisible'   => $isInvisible
        ]);
    }
}
