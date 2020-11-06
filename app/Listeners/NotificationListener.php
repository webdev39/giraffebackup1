<?php

namespace App\Listeners;

use App\Events\CreatedCommentEvent;
use App\Events\Eloquent\BaseEloquentEvent;
use App\Events\Eloquent\Deleted\DeletedUserTenantTaskEvent;
use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedBoardEvent;
use App\Events\Eloquent\Saved\SavedBudgetEvent;
use App\Events\Eloquent\Saved\SavedGroupEvent;
use App\Events\Eloquent\Saved\SavedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedTaskEvent;
use App\Events\Eloquent\Saved\SavedUserTenantTaskEvent;
use App\Events\GroupCommentPostedEvent;
use App\Events\LikedCommentEvent;
use App\Events\TaskChangeToNotificationEvent;
use App\Events\TaskSubscriptionAndAssignmentEvent;
use App\Models\Task;
use App\Notifications\ChangeHardBudgetNotification;
use App\Notifications\ChangeSoftBudgetNotification;
use App\Notifications\CommentLikeNotification;
use App\Notifications\CommentMentionNotification;
use App\Notifications\Fcm\CommentMentionNotification as CommentMentionNotificationFcm;
use App\Notifications\GroupCommentPostedNotification;
use App\Notifications\PrioritizeTaskNotification;
use App\Notifications\RenameTaskNotification;
use App\Notifications\TaskAttachedNotification;
use App\Notifications\TaskSubscribeNotifications;
use App\Notifications\TaskSubscriptionAndAssignmentNotification;
use App\Notifications\TaskWorkflowNotification;
use App\Services\Notification\NotificationService;
use App\Services\TaskChangesQueue;
use App\TaskChangeNotificationFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationListener implements ShouldQueue
{
    const TASK_QUEUE_NOTIFICATIONS = [
        RenameTaskNotification::class,
        PrioritizeTaskNotification::class,
        ChangeSoftBudgetNotification::class,
        ChangeHardBudgetNotification::class,
        TaskSubscribeNotifications::class,
        TaskWorkflowNotification::class,
        TaskAttachedNotification::class,
    ];

    /** @var NotificationService  */
    private $notifyService;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'notifications';
    /**
     * @var NotificationQueue
     */
    private $taskChangesQueueService;

    /**
     * NotificationListener constructor.
     * @param TaskChangesQueue $taskChangesQueueService
     */
    public function __construct(TaskChangesQueue $taskChangesQueueService)
    {
        $this->notifyService  = app('NotificationSer');
        $this->taskChangesQueueService = $taskChangesQueueService;
    }

    public function likedComment(LikedCommentEvent $event)
    {
        $subscribers = app('UserSer')->getUserByIds([$event->comment->user->id])->where('id', '!=', $event->user->id);

        Notification::send($subscribers, new CommentLikeNotification($event));
    }

    /**
     * @param CreatedCommentEvent $event
     */
    public function createdComment(CreatedCommentEvent $event)
    {
        $mentions = app('UserSer')->getUserByIds($event->mentionIds)->where('id', '!=', $event->user->id);

        Notification::send($mentions, new CommentMentionNotification($event));

        $devicesTokens = collect([]);
        foreach($mentions as $user) {
            $devicesTokens = $devicesTokens->merge($user->devicesTokens);
        }

        if($devicesTokens->count()) {
            try {
                Notification::send($devicesTokens->unique(), new CommentMentionNotificationFcm($event));
            } catch (\Exception $exception) {
                Log::error($exception->getMessage(), [$exception->getFile(), $exception->getLine(), $exception->getCode()]);
            }
        }
    }

    public function groupCommentPosted(GroupCommentPostedEvent $event)
    {
        $group = $event->comment->group->fresh();
        $notifiable = $group->members->pluck('user')->unique();
        Notification::send($notifiable, new GroupCommentPostedNotification($event));
    }

    /**
     * @param SavedNotificationSubscriptionEvent $event
     */
    public function notifySubscribe(SavedNotificationSubscriptionEvent $event)
    {
        $task = $event->model->task;

        if ($event->user && $task && $task->draft == 0) {
            $subscribers = $this->notifyService->getActiveSubscribersByTaskId($task->id)
                ->where('id', '=', $event->model->user->id)
                ->where('id', '!=', $event->user->id);

            $event->setDefaultNotifyClass(TaskSubscribeNotifications::class, 0);

            $this->sendNotifications($event, $task, $subscribers);
        }
    }

    /**
     * @param DeletedNotificationSubscriptionEvent $event
     */
    public function notifyUnSubscribe(DeletedNotificationSubscriptionEvent $event)
    {
        $task = $event->model->task;

        if ($event->user && $task && $task->draft == 0) {
            $subscribers = $this->notifyService->getActiveSubscribersByTaskId($task->id)
                ->where('id', '=', $event->model->user->id)
                ->where('id', '!=', $event->user->id);

            $event->setDefaultNotifyClass(TaskSubscribeNotifications::class, 1);

            $this->sendNotifications($event, $task, $subscribers);
        }
    }

    /**
     * @param BaseEloquentEvent $event
     */
    public function taskSubscribe(BaseEloquentEvent $event)
    {
        $task = $event->model->task;

        if ($event->user && $task && $task->draft == 0) {
            $subscribers = $this->notifyService->getActiveSubscribersByTaskId($task->id)
                ->where('id', '!=', $event->user->id);

            $event->setDefaultNotifyClass(TaskAttachedNotification::class, false);

            if($event instanceof SavedUserTenantTaskEvent) {
                $event->notify['default']['old_value'] = 0;
                $event->notify['default']['new_value'] = $event->attachUser->id;
            }

            if($event instanceof DeletedUserTenantTaskEvent) {
                $event->notify['default']['old_value'] = $event->detachUser->id;
                $event->notify['default']['new_value'] = 0;
            }

            $this->sendNotifications($event, $task, $subscribers);
        }
    }

    /**
     * @param SavedTaskEvent $event
     */
    public function savedTask(SavedTaskEvent $event)
    {
        if ($event->notify && $event->user && $event->model->draft == 0) {
            $subscribers = $this->notifyService->getActiveSubscribersByTaskId($event->model->id)
                ->where('id', '!=', $event->user->id);

            $this->sendNotifications($event, $event->model, $subscribers);
        }
    }

    public function taskSubscriptionAndAssignment(TaskSubscriptionAndAssignmentEvent $event)
    {
        $subscribers = $this->notifyService->getActiveSubscribersByTaskId($event->task->id)
            ->where('id', '!=', $event->sender->id);

        foreach ($subscribers as $subscriber) {
            $subscriber->notify(new TaskSubscriptionAndAssignmentNotification($event->sender, $event->task, $event->user));
        }
    }

    /**
     * @param SavedBudgetEvent $event
     */
    public function savedBudget(SavedBudgetEvent $event)
    {
        if ($event->notify && $event->user) {
            $task = $event->model->task()->first();

            if ($task && $task->draft == 0) {
                $subscribers = $this->notifyService->getActiveSubscribersByTaskId($task->id)
                    ->where('id', '!=', $event->user->id);

                $this->sendNotifications($event, $task, $subscribers);
            }
        }
    }

    /**
     * @param SavedBoardEvent $event
     */
    public function savedBoard(SavedBoardEvent $event)
    {
        if ($event->notify && $event->user) {
            $subscribers = $this->notifyService->getActiveSubscribersByBoardId($event->model->id)
                ->where('id', '!=', $event->user->id);

            $this->sendNotifications($event, $event->model, $subscribers);
        }
    }

    /**
     * @param SavedGroupEvent $event
     */
    public function savedGroup(SavedGroupEvent $event)
    {
        if ($event->notify && $event->user) {
            $subscribers = $this->notifyService->getActiveSubscribersByGroupId($event->model->id)
                ->where('id', '!=', $event->user->id);

            $this->sendNotifications($event, $event->model, $subscribers);
        }
    }

    public function taskChangeToNotification(TaskChangeToNotificationEvent $event): void
    {
        $notification = TaskChangeNotificationFactory::createFromEvent($event);

        Notification::send(
            $this->notifyService->getActiveSubscribersByIds($event->subscribers),
            $notification
        );
    }

    /**
     * Sending notifications
     *
     * @param BaseEloquentEvent $event
     * @param Model             $model
     * @param Collection        $subscribers
     */
    private function sendNotifications(BaseEloquentEvent $event, Model $model, Collection $subscribers)
    {
        if ($model instanceof Task && $model->draft != 0 || is_null($event->user)) {
            return;
        }

        foreach ($event->notify as $field => $notify) {
            if(in_array($notify['class'], self::TASK_QUEUE_NOTIFICATIONS, true)) {
                $this->taskChangesQueueService->push(
                    $event->user,
                    $model,
                    $field,
                    $subscribers->pluck('id'),
                    $notify['class'],
                    isset($notify['new_value']) ? $notify['new_value'] : null,
                    isset($notify['old_value']) ? $notify['old_value'] : null
                );
            } else {
                $notification = new $notify['class']($event, $model, $field);
                Notification::send($subscribers, $notification);
            }
        }
    }
}
