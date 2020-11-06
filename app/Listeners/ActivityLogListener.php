<?php

namespace App\Listeners;

use App\Events\Eloquent\Deleted\DeletedUserTenantTaskEvent;
use App\Events\Eloquent\Saved\CreatedTaskEvent;
use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedBoardEvent;
use App\Events\Eloquent\Saved\SavedBudgetEvent;
use App\Events\Eloquent\Saved\SavedGroupEvent;
use App\Events\Eloquent\Saved\SavedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedSubTaskEvent;
use App\Events\Eloquent\Saved\SavedTaskEvent;
use App\Events\Eloquent\Saved\SavedUserTenantTaskEvent;
use App\Services\ActivityLog\ActivityLogService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivityLogListener implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'logs';

    /**
     * @param SavedUserTenantTaskEvent|DeletedUserTenantTaskEvent $event
     */
    public function taskSubscribe($event)
    {
        $task = $event->model->task;
        if($event instanceof SavedNotificationSubscriptionEvent) {
            $event->attach = true;
            $user = $event->model->user;
        } elseif($event instanceof DeletedUserTenantTaskEvent || $event instanceof DeletedNotificationSubscriptionEvent) {
            $user = $event->model->user;
        } else {
            $user = $event->model->userTenant->user;
        }

        if ($event->user && $task && $task->draft == 0) {
            if($event instanceof SavedUserTenantTaskEvent) {
                ActivityLogService::taskAttachAction($event, $task, $user);
            } elseif($event instanceof DeletedUserTenantTaskEvent || $event instanceof DeletedNotificationSubscriptionEvent) {
                ActivityLogService::taskDetachAction($event, $task, $user);
            } else {
                ActivityLogService::taskSubscribeAction($event, $task, $user);
            }
        }
    }

    public function taskSubscriptionAndAssignment($event)
    {
        $task = $event->task;
        $user = $event->user;

        ActivityLogService::taskSubscribeAndAttachAction($event, $task, $user);
    }

    /**
     * @param SavedBudgetEvent $event
     */
    public function savedBudget(SavedBudgetEvent $event)
    {
        if ($event->wasRecentlyCreated) {
            return;
        }

        if ($event->activity) {
            $task = $event->model->task()->first();

            if ($event->user && $task && $task->draft == 0) {
                ActivityLogService::changeTaskAction($event, $task);
            }
        }
    }

    /**
     * @param CreatedTaskEvent $event
     */
    public function createdTask(CreatedTaskEvent $event)
    {
        $task = $event->model;

        $activity = activity()->useLog($task->logName)->performedOn($task)->withProperties(collect([
            'action'    => 'created',
            'task_id'   => $task->id,
        ]));
        if ($task->creator) {
            app('ActivityLogSer')->taskCreated($task, $task->creator);
        }
    }

    /**
     * @param SavedTaskEvent $event
     */
    public function savedTask(SavedTaskEvent $event)
    {
        if ($event->wasRecentlyCreated) {
            return;
        }

        if ($event->activity && $event->user && $event->draft == 0) {
            ActivityLogService::changeTaskAction($event, $event->model);
        }
    }

    /**
     * @param SavedSubTaskEvent $event
     */
    public function createdSubTask(SavedSubTaskEvent $event)
    {
        $subTask = $event->model;

        $activity = activity()->useLog($subTask->logName)->performedOn($subTask)->withProperties(collect([
            'action'    => 'created',
            'task_id'   => $subTask->task_id,
        ]));
        if ($subTask->creator && !$subTask->is_completed) {
            $activity->causedBy($subTask->creator);
            $activity->log('Sub task "'.$subTask->name.'" created by '.$subTask->creator->full_name );
        }
    }

    /**
     * @param SavedSubTaskEvent $event
     */
    public function completedSubTask(SavedSubTaskEvent $event)
    {
        $subTask = $event->model;

        if ($subTask->is_completed) {
            $activity = activity()->useLog($subTask->logName)->performedOn($subTask)->withProperties(collect([
                'action'    => 'created',
                'task_id'   => $subTask->task_id,
            ]));
            $activity->causedBy($subTask->completedBy);
            $activity->log('Sub task "'.$subTask->name.'" completed by ' .$subTask->completedBy->full_name );
        }
    }

    /**
     * @param SavedBoardEvent $event
     */
    public function savedBoard(SavedBoardEvent $event)
    {
        if ($event->wasRecentlyCreated) {
            return;
        }

        if ($event->activity && $event->user) {
            ActivityLogService::changeBoardAction($event, $event->model);
        }
    }

    /**
     * @param SavedGroupEvent $event
     */
    public function savedGroup(SavedGroupEvent $event)
    {
        if ($event->wasRecentlyCreated) {
            return;
        }

        if ($event->activity && $event->user) {
            ActivityLogService::changeGroupAction($event, $event->model);
        }
    }
}
