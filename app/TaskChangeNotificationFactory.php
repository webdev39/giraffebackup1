<?php

namespace App;

use App\Events\TaskChangeToNotificationEvent;
use App\Notifications\BaseNotification;
use App\Notifications\ChangeHardBudgetNotification;
use App\Notifications\ChangeSoftBudgetNotification;
use App\Notifications\PrioritizeTaskNotification;
use App\Notifications\RenameTaskNotification;
use App\Notifications\TaskSubscribeNotifications;
use App\Notifications\TaskWorkflowNotification;
use http\Exception\InvalidArgumentException;
use App\Notifications\TaskAttachedNotification;

class TaskChangeNotificationFactory
{
    public static function createFromEvent(TaskChangeToNotificationEvent $event): BaseNotification
    {
        $className = $event->notificationClass;
        switch ($className) {
            case RenameTaskNotification::class:
            case PrioritizeTaskNotification::class:
            case ChangeHardBudgetNotification::class:
            case ChangeSoftBudgetNotification::class:
            case TaskWorkflowNotification::class:
                return static::asBaseTaskNotification($event);
            case TaskAttachedNotification::class:
                return static::asTaskAttachedNotification($event);
            case TaskSubscribeNotifications::class:
                return static::asTaskChangeSubscriptionNotification($event);
            default:
                throw new InvalidArgumentException('Changes to notification ' . $className . ' could not be converted into one notification');
        }
    }

    public static function asBaseTaskNotification(TaskChangeToNotificationEvent $event): BaseNotification
    {
        return new $event->notificationClass($event->sender, $event->task, $event->newValue, $event->oldValue);
    }

    public static function asTaskAttachedNotification(TaskChangeToNotificationEvent $event): BaseNotification
    {
        return new $event->notificationClass($event->sender, $event->task, $event->newValue, $event->oldValue);
    }

    private static function asTaskChangeSubscriptionNotification(TaskChangeToNotificationEvent $event)
    {
        return new $event->notificationClass($event->sender, $event->task, $event->oldValue);
    }
}