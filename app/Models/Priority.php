<?php

namespace App\Models;

/**
 * App\Models\Priority
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $color
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $is_default
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Board[] $board
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_primary
 * @property int|null $sort_order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Priority whereSortOrder($value)
 */
class Priority extends BaseModel
{
    protected $table = 'priorities';

    protected $fillable = [
        'name',
        'description',
        'color',
        'sort_order',
        'is_default',
        'is_primary',
    ];

    public function board()
    {
        return $this->belongsToMany(Board::class, (new BoardPriority())->getTable(), 'priority_id', 'board_id');
    }
}
