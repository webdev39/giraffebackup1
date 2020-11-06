<?php

namespace App\Models;

use App\HasPermissions;
use App\Services\CustomEntrustUserTrait;
use App\Services\AbilityService;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\UserTenantGroup
 *
 * @property int $id
 * @property int|null $user_tenant_id
 * @property int|null $group_id
 * @property int $is_creator
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \App\Models\UserTenant|null $userTenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereIsCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup whereUserTenantId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroup query()
 */
class UserTenantGroup extends BaseModel implements HasPermissions
{
    use CustomEntrustUserTrait; // add this trait to your user model
    protected $entityName = Role::USER_TENANT_GROUP_ENTITY;

    protected $fillable = [
        'user_tenant_id',
        'group_id',
        'is_creator'
    ];

    protected $table = 'user_tenant_group';

    public function userTenant()
    {
        return $this->belongsTo(UserTenant::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @param Builder $builder
     * @param int $userTenantId
     * @param int $groupId
     * @return Builder
     */
    public function scopeForUserTenantAndGroup(Builder $builder, $userTenantId, $groupId)
    {
        return $builder->where([
            'user_tenant_id' => $userTenantId,
            'group_id'       => $groupId
        ]);
    }

    /**
     * @param $permissionCode
     * @return mixed
     */
    public function able($permissionCode)
    {
        return app(AbilityService::class)->hasPermission($permissionCode, $this);
    }
}
