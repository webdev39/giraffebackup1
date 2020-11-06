<?php

namespace App\Repositories;

use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Models\NotificationSubscription;
use App\Models\Task;
use Illuminate\Support\Collection;

/**
 * Class NotificationSubscriptionRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property NotificationSubscription $model
 */
class NotificationSubscriptionRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NotificationSubscription::class;
    }

    /**
     * @param Task $task
     * @param bool $active
     *
     * @return Collection
     */
    public function getSubscriptionsByTask(Task $task, bool $active = true) : Collection
    {
        return $task->notifySubscriptions;
    }

    /**
     * @param Task $task
     * @param int  $userId
     * @param bool $active
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function changeSubscriptionStatus(Task $task, int $userId, bool $active = true)
    {
        if ($active) {
            return $task->notifySubscriptions()->updateOrCreate(['user_id' => $userId]);
        }

        $notificationSubscription = $task->notifySubscriptions()->where('user_id', $userId)->first();
        return $notificationSubscription->delete();
    }

    /**
     * @param array $taskIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeSubscribersByTaskIds(array $taskIds)
    {
        return $this->model->withoutGlobalScopes()->whereIn('task_id', $taskIds)->delete();
    }

    /**
     * @param array $taskIds
     * @param array $userIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function unsubscribeUserFromTasks(array $taskIds, array $userIds)
    {
        return $this->model
            ->whereIn('task_id', $taskIds)
            ->whereIn('user_id', $userIds)
            ->delete();
    }
}
