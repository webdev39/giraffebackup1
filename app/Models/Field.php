<?php

namespace App\Models;

/**
 * Class Field
 *
 * @package App\Models
 * @property int $id
 * @property string $type
 * @property int|null $group
 * @property string $key
 * @property string $name
 * @property string $description
 * @property int $is_active
 * @property int $is_public
 * @property int $is_default
 * @property int $is_archived
 * @property int|null $user_tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserTenant|null $userTenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Field whereUserTenantId($value)
 * @mixin \Eloquent
 */
class Field extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'group',
        'key',
        'name',
        'description',
        'is_active',
        'is_public',
        'is_default',
        'is_archived',
        'user_tenant_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTenant()
    {
        return $this->belongsTo(UserTenant::class);
    }
}
