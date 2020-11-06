<?php

namespace App\Models;

/**
 * App\Models\BoardPriority
 *
 * @property int $id
 * @property int $board_id
 * @property int $priority_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority whereBoardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardPriority query()
 */
class BoardPriority extends BaseModel
{
    protected $fillable = [
        'board_id',
        'priority_id'
    ];

    protected $table = 'board_priority';
}