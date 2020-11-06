<?php

namespace App\Models;

/**
 * App\Models\UserTenantGroupRole
 *
 * @property int $id
 * @property int|null $user_tenant_group_id
 * @property int|null $role_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole whereUserTenantGroupId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantGroupRole query()
 */
class UserTenantGroupRole extends BaseModel
{
    protected $fillable = ['user_tenant_group_id', 'role_id'];
    protected $table = 'user_tenant_group_role';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenantGroup()
    {
        return $this->belongsTo(UserTenantGroup::class);
    }
}
