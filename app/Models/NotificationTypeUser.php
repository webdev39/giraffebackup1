<?php

namespace App\Models;

/**
 * Class NotificationTypeUser
 *
 * @package App\Models
 * @property int $user_id
 * @property int $notification_type_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationTypeUser whereNotificationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationTypeUser whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationTypeUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationTypeUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationTypeUser query()
 */
class NotificationTypeUser extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_type_user';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
