<?php

namespace App\Models;

/**
 * App\Models\Reactions
 *
 * @property int $id
 * @property int $targetable_id
 * @property string $targetable_type
 * @property int|null $sourceable_id
 * @property string|null $sourceable_type
 * @property int $user_tenant_id
 * @property string $reaction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereSourceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereSourceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereTargetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereTargetableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reactions whereUserTenantId($value)
 * @mixin \Eloquent
 */
class Reactions extends BaseModel
{
    /**
     * Reactions
     */
    const STICK = 'stick';
    const LIKE  = 'like';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'targetable_id',
        'targetable_type',
        'sourceable_id',
        'sourceable_type',
        'user_tenant_id',
        'reaction',
        'created_at',
        'updated_at',
    ];

    public function targetable()
    {
        return $this->morphTo();
    }
}
