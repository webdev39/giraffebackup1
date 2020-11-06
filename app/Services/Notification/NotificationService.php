<?php

namespace App\Services\Notification;

use App\Events\Eloquent\ChangedTaskEvent;
use App\Models\Task;
use App\Models\User;
use App\Repositories\NotificationSubscriptionRepositoryEloquent;
use Illuminate\Support\Collection;

class NotificationService
{
    /** @var NotificationSubscriptionRepositoryEloquent $notificationSubscriptionRepo */
    private $notificationSubscriptionRepo;

    /**
     * NotificationService constructor.
     */
    public function __construct()
    {
        $this->notificationSubscriptionRepo = app('NotificationSubscriptionRepo');
    }

    /**
     * @param User   $user
     * @param string $notifyId
     * @param string $status
     */
    public function changeStatusNotification(User $user, string $notifyId, string $status)
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $user->notifications()->where('id', $notifyId)->first();

        if ($status == 'read') {
            $notification->markAsRead();
        } else if ($status == 'unread') {
            $notification->markAsUnread();
        }
    }

    /**
     * @param User $user
     */
    public function enableAllNotifications(User $user)
    {
        if ($typeIds = app('FieldRepo')->getNotificationTypes()->pluck('id')->toArray()) {
            return $user->notificationType()->attach($typeIds);
        }
    }

    /**
     * @param Task     $task
     * @param int|null $userId
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function taskSubscribe(Task $task, int $userId)
    {
        $this->notificationSubscriptionRepo->changeSubscriptionStatus($task, $userId, true);
        return event(new ChangedTaskEvent($task));
    }

    /**
     * @param Task     $task
     * @param int|null $userId
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function taskUnsubscribe(Task $task, int $userId)
    {
        $this->notificationSubscriptionRepo->changeSubscriptionStatus($task, $userId, false);
        return event(new ChangedTaskEvent($task));
    }

    /**
     * @param Task $task
     * @param bool $active
     *
     * @return Collection
     */
    public function getSubscriptionsByTask(Task $task, bool $active = true) : Collection
    {
        return $this->notificationSubscriptionRepo->getSubscriptionsByTask($task, $active);
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getActiveSubscribersByTaskIds(array $taskIds) : Collection
    {
        $result         = collect();
        $subscribers    = app('UserRepo')->getActiveSubscribersByTaskIds($taskIds);

        foreach ($subscribers as $subscriber) {
            $result->push(User::createFromStd($subscriber));
        }

        return $result;
    }

    /**
     * @param int $taskId
     *
     * @return Collection
     */
    public function getActiveSubscribersByTaskId(int $taskId) : Collection
    {
        return $this->getActiveSubscribersByTaskIds([$taskId]);
    }

    /**
     * @param int $boardId
     *
     * @return Collection
     */
    public function getActiveSubscribersByBoardId(int $boardId) : Collection
    {
        $tasks       = app('TaskRepo')->getTasksByBoardIds([$boardId]);
        $taskIds     = $tasks->pluck('id')->unique()->toArray();

        return $this->getActiveSubscribersByTaskIds($taskIds);
    }

    /**
     * @param int $groupId
     *
     * @return Collection
     */
    public function getActiveSubscribersByGroupId(int $groupId) : Collection
    {
        $boards      = app('BoardRepo')->getBoardsByGroupIds([$groupId]);
        $boardIds    = $boards->pluck('id')->unique()->toArray();

        $tasks       = app('TaskRepo')->getTasksByBoardIds($boardIds);
        $taskIds     = $tasks->pluck('id')->unique()->toArray();

        return $this->getActiveSubscribersByTaskIds($taskIds);
    }

    public function getActiveSubscribersByIds(array $ids): Collection
    {
        $result         = collect();
        $subscribers    = app('UserRepo')->getActiveSubscribersByIds($ids);

        foreach ($subscribers as $subscriber) {
            $result->push(User::createFromStd($subscriber));
        }

        return $result;
    }
}
