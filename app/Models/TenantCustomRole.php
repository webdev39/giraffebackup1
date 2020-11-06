<?php

namespace App\Models;

/**
 * App\Models\TenantCustomRole
 *
 * @property int $id
 * @property int|null $tenant_id
 * @property int|null $role_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TenantCustomRole query()
 */
class TenantCustomRole extends BaseModel
{
    protected $fillable = ['tenant_id', 'role_id'];
    protected $table = 'tenant_custom_role';

}
