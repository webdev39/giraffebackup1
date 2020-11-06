<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserProfile[] $userProfile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType query()
 */
class NotificationType extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function userProfile()
    {
        return $this->belongsToMany(UserProfile::class);
    }
}
