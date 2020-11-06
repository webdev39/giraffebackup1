<?php

namespace App\Models;

/**
 * App\Models\UserTenantTemplates
 *
 * @property-read \App\Models\UserTenant $user_tenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_tenant_id
 * @property string $type
 * @property string $key
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantTemplates whereUserTenantId($value)
 */
class UserTenantTemplates extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_tenant_id',
        'type',
        'key',
        'content',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_tenant()
    {
        return $this->belongsTo(UserTenant::class);
    }
}
