<?php

namespace App\Models;

/**
 * App\Models\PipelineFilter
 *
 * @property int $id
 * @property int $is_active
 * @property string $name
 * @property string $display_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PipelineRule[] $rules
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineFilter query()
 */
class PipelineFilter extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_active',
        'name',
        'display_name'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pipeline_filters';

    /**
     * Get the associated filter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules()
    {
        return $this->hasMany(PipelineRule::class);
    }
}
