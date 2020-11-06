<?php

namespace App\Models;

use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Events\Eloquent\Saved\SavedNotificationSubscriptionEvent;

/**
 * App\Models\NotificationSubscription
 *
 * @property int $id
 * @property int $active
 * @property int $task_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User $userTenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationSubscription query()
 */
class NotificationSubscription extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'task_id',
        'user_id',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved'     => SavedNotificationSubscriptionEvent::class,
        'deleting'  => DeletedNotificationSubscriptionEvent::class
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenant()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
