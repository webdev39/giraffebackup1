<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class BoardCollection extends BaseCollection
{
    /**
     * @param TaskCollection $collection
     * @param int            $userTenantId
     *
     * @return $this
     */
    public function calcCountOpenTasksForUserTenant(TaskCollection $collection, int $userTenantId)
    {
        return $this->mutator(function(array $value) use ($collection, $userTenantId)
        {
            if ($tasks = $collection->get($value['id'])) {
                /** @var Collection $filtered */
                $filtered = $tasks->filter(function ($task) use ($userTenantId) {
                    return $task->taskSubscribers->where('user_tenant_id', $userTenantId)->count() > 0;
                });

                $value['openTaskCounter'] = $filtered->count();
            }

            return $value;
        });
    }
}