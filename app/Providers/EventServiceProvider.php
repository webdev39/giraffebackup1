<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\TenantIsAdded' => [
            'App\Listeners\TenantListener@tenantIsAdded'
        ],
        'App\Events\UserTenantHasBeenCreated' => [
            'App\Listeners\UserTenantListener@createDefaultData'
        ],

        'App\Events\CreatedCommentEvent' => [
            'App\Listeners\NotificationListener@createdComment',
        ],

        'App\Events\GroupCommentPostedEvent' => [
            'App\Listeners\NotificationListener@groupCommentPosted',
        ],

        'App\Events\LikedCommentEvent' => [
            'App\Listeners\NotificationListener@likedComment',
        ],

        /** Eloquent Event */
        'App\Events\Eloquent\Saved\SavedBoardEvent' => [
            'App\Listeners\ActivityLogListener@savedBoard',
            'App\Listeners\NotificationListener@savedBoard',
            'App\Listeners\TagsListener@savedBoard',
        ],
        'App\Events\Eloquent\Saved\SavedBudgetEvent' => [
            'App\Listeners\ActivityLogListener@savedBudget',
            'App\Listeners\NotificationListener@savedBudget',
        ],
        'App\Events\Eloquent\Saved\SavedGroupEvent' => [
            'App\Listeners\ActivityLogListener@savedGroup',
            'App\Listeners\NotificationListener@savedGroup',
            'App\Listeners\TagsListener@savedGroup',
        ],
        'App\Events\Eloquent\Saved\CreatedTaskEvent' => [
            'App\Listeners\ActivityLogListener@createdTask',
        ],
        'App\Events\Eloquent\Saved\SavedTaskEvent' => [
            'App\Listeners\ActivityLogListener@savedTask',
            'App\Listeners\NotificationListener@savedTask',
            'App\Listeners\TagsListener@savedTask',
        ],

        'App\Events\TaskSubscriptionAndAssignmentEvent' => [
            'App\Listeners\NotificationListener@taskSubscriptionAndAssignment',
            'App\Listeners\ActivityLogListener@taskSubscriptionAndAssignment',
        ],

        'App\Events\Eloquent\Saved\SavedCommentEvent' => [
            'App\Listeners\TagsListener@savedComment',
        ],

        'App\Events\TaskChangeToNotificationEvent' => [
            'App\Listeners\NotificationListener@taskChangeToNotification',
        ],
        'App\Events\Eloquent\Saved\SavedSubTaskEvent' => [
            'App\Listeners\ActivityLogListener@createdSubTask',
            'App\Listeners\ActivityLogListener@completedSubTask',
        ],
        'App\Events\Eloquent\Saved\SavedUserTenantTaskEvent' => [
            'App\Listeners\ActivityLogListener@taskSubscribe',
            'App\Listeners\NotificationListener@taskSubscribe',
        ],
        'App\Events\Eloquent\Deleted\DeletedUserTenantTaskEvent' => [
            'App\Listeners\ActivityLogListener@taskSubscribe',
            'App\Listeners\NotificationListener@taskSubscribe',
        ],
        'App\Events\Eloquent\Saved\SavedNotificationSubscriptionEvent' => [
            'App\Listeners\NotificationListener@notifySubscribe',
            'App\Listeners\ActivityLogListener@taskSubscribe',
        ],
        'App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent' => [
            'App\Listeners\ActivityLogListener@taskSubscribe',
            'App\Listeners\NotificationListener@notifyUnSubscribe',
        ],
        'Illuminate\Notifications\Events\NotificationSent' => [
            // 'App\Listeners\LogNotification',
        ],
        'App\Events\Eloquent\ChangedTaskEvent' => [
            'App\Listeners\TaskActivityLogListener@updateTaskLog',
        ],
    ];


    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\TimerEventSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
