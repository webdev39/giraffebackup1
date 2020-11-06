<?php

namespace App\Models;

/**
 * App\Models\UserTenantRole
 *
 * @property int $id
 * @property int|null $user_tenant_id
 * @property int|null $role_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereUserTenantId($value)
 * @mixin \Eloquent
 * @property int $can_invited
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole whereCanInvited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantRole query()
 */
class UserTenantRole extends BaseModel
{
    protected $fillable = ['user_tenant_id', 'role_id', 'can_invited'];
    protected $table    = 'user_tenant_role';
}
