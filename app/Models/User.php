<?php

namespace App\Models;

use App\Services\AbilityService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $last_name
 * @property int $is_confirmed
 * @property string|null $confirm_hash
 * @property int $chosen_tenant_id
 * @property string|null $last_activity
 * @property-read mixed $full_name
 * @property-read mixed $user_tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSubscription[] $notificationSubscriptions
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant[] $tenants
 * @property-read \App\Models\UserProfile $userProfile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereChosenTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConfirmHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $nickname
 * @property-read string $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationType[] $notificationType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereNickname($value)
 * @property int $utc_offset
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUtcOffset($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSubscription[] $notifySubscriptions
 * @property false|int|string $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @property-read \Illuminate\Database\Eloquent\Collection|\NotificationChannels\WebPush\PushSubscription[] $pushSubscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillTimer[] $billTimer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSubscription[] $notificationSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationTypeUser[] $notificationTypeUser
 * @property-read Collection $devicesTokens
 * @property int $selected_color_scheme_id
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    use Notifiable; use HasPushSubscriptions;

    /**
     * User status list
     */
    const STATUS = [
        'inactive'  => 0,
        'active'    => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'name',
        'last_name',
        'nickname',
        'is_confirmed',
        'confirm_hash',
        'chosen_tenant_id',
        'last_activity',
        'utc_offset',
        'status',
        'selected_color_scheme_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends= [
        'userTenant',
        'avatar',
        'can_invited'
    ];

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'user.'.$this->id;
    }

    /**
     * @return UserTenant
     */
    public function getChosenTenant() : ?UserTenant
    {
        $query = [
            'user_id'   => $this->id,
            'tenant_id' => $this->chosen_tenant_id
        ];

        return Cache::remember($this->cacheKey($query, 'chosen_tenant'), 15, function() use ($query) {
            return UserTenant::withoutGlobalScopes()->where($query)->first();
        });
    }

    /**
     * @return HasMany
     */
    public function userTenant() : HasMany
    {
        return $this->hasMany(UserTenant::class);
    }

    /**
     * @return UserTenant|null
     */
    public function getUserTenantAttribute()
    {
        return $this->chosen_tenant_id ? $this->getChosenTenant() : null;
    }

    /**
     * @param $nickname
     *
     * @return string
     */
    public function getNicknameAttribute($nickname)
    {
        return UserService::getUserNickname($nickname, $this);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    /**
     * @return string
     */
    public function getAvatarAttribute()
    {
        return optional($this->userProfile->avatar)->url;
    }

    /**
     * @param $status
     *
     * @return false|int|string
     */
    public function getStatusAttribute($status)
    {
        return array_search($status, self::STATUS);
    }

    /**
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = $password ? bcrypt($password) : null;
    }

    /**
     * @param $value
     */
    public function setConfirmHashAttribute($value)
    {
        $this->attributes['confirm_hash'] = $value ? md5(str_random(32)) : null;
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return optional($this->user_tenant)->is_owner ?? false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifySubscriptions()
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notificationType()
    {
        return $this->belongsToMany(Field::class, 'notification_type_user', 'user_id', 'notification_type_id');
    }






    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'creator_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function billTimer()
    {
        return $this->hasMany(BillTimer::class);
    }

    public function notificationSubscription()
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    public function notificationTypeUser()
    {
        return $this->hasMany(NotificationTypeUser::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }








    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, (new UserTenant())->getTable(), 'user_id', 'tenant_id');
    }

    public function createPasswordResetToken()
    {
        $date = Carbon::now()->toDateTimeString();
        $token = str_random(64);
        $passwordReset = PasswordReset::create(['email' => $this->email, 'token' => $token, 'created_at' => $date]);
        return $passwordReset->token;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, (new TenantCustomRole())->getTable(), 'user_id', 'role_id');
    }

    public function canReceiveNotificationForTask($taskId)
    {
        $result =  $this->notifySubscriptions->search(function ($item) use ($taskId) {
            return $item->task_id === $taskId;
        });

        return $result !== false;
    }

    /**
     * @param $permissionCode
     * @return mixed
     */
    public function able($permissionCode)
    {
        return app(AbilityService::class)->hasPermission($permissionCode, $this->getChosenTenant());
    }

    /**
     * @return bool
     */
    public function getCanInvitedAttribute()
    {
        return (bool) optional($this->userTenant)->can_invited;
    }

    public function colorSchemes(): HasMany
    {
        return $this->hasMany(UserColorScheme::class);
    }

    public function colorSchema(): UserColorScheme
    {
        if(!$colorScheme = UserColorScheme::find($this->selected_color_scheme_id)) {
            $colorScheme = UserColorScheme::whereDefault(1)->first();
        }

        return  $colorScheme;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->whereStatus(self::STATUS['active']);
    }

    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        $query = $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
        if(!$this->able('TIME_TRACKING')) {
            $query->whereNotIn('type', [
                'App\Notifications\ChangeSoftBudgetNotification',
                'App\Notifications\ChangeHardBudgetNotification'
            ]);
        }

        return $query;
    }

    public function devicesTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }
}
