<?php

namespace App\Models;

use App\HasPermissions;
use App\Services\CustomEntrustUserTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\UserTenant
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $tenant_id
 * @property string|null $invite_hash
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $is_owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $archivedGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $doneTasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Filter[] $filters
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $openTasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\Tenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Timer[] $timers
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenantGroup[] $userTenantGroups
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereInviteHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereIsOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereUserId($value)
 * @mixin \Eloquent
 * @property-read bool $can_invited
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Field[] $settings
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant query()
 * @property string|null $company_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenant whereCompanyName($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenantRole[] $userTenantRoles
 */
class UserTenant extends BaseModel implements HasPermissions
{
    use CustomEntrustUserTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tenant_id',
        'is_owner',
        'invite_hash',
        'company_name',
        'can_invited'
    ];

    /**
     * @param $value
     */
    public function setInviteHashAttribute($value)
    {
        $this->attributes['invite_hash'] = $value ? md5(str_random(32)) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timers()
    {
        return $this->hasMany(Timer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany(Field::class)->where('type', 'setting');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function filters()
    {
        return $this->hasMany(Filter::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTenantGroups()
    {
        return $this->hasMany(UserTenantGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTenantRoles()
    {
        return $this->hasMany(UserTenantRole::class);
    }




    /**
     * @return mixed
     */
    public function getTenantOwner()
    {
        return $this->tenant->owner();
    }

    protected $entityName = Role::USER_TENANT_ENTITY;

    protected static function boot()
    {
        parent::boot();

        if (!request()->has('invite_user') || request()->get('invite_user') === false) {
            static::addGlobalScope('invited_user', function (Builder $builder) {
                $builder->where('invite_hash', '=', null);
            });
        }
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, (new UserTenantGroup())->getTable(),
            'user_tenant_id',
            'group_id'
        )
            ->where('is_archive', 0)
            ->withPivot(['is_creator', 'id as userTenantGroupId']);
    }

    public function archivedGroups()
    {
        return $this->belongsToMany(
            Group::class,
            (new UserTenantGroup())->getTable(),
            'user_tenant_id',
            'group_id'
        )->where('is_archive', 1);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, (new UserTask())->getTable(), 'task_id', 'user_tenant_id')->where('is_archive',0);
    }

    public function openTasks()
    {
        return $this->belongsToMany(Task::class, (new UserTask())->getTable(), 'task_id', 'user_tenant_id')->whereNull('done_by')->where('is_archive',0);
    }

    public function doneTasks()
    {
        return $this->belongsToMany(Task::class, (new UserTask())->getTable(), 'task_id', 'user_tenant_id')->where('done_by', '!=',null)->where('is_archive',0);
    }

    public function getCanInvitedAttribute()
    {
        return $this->can(Permission::CAN_INVITE_OTHERS_PERMISSIONS) || $this->is_owner;
    }
}
