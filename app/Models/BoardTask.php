<?php

namespace App\Models;

/**
 * App\Models\BoardTask
 *
 * @property int $id
 * @property int|null $board_id
 * @property int|null $task_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask whereBoardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoardTask query()
 */
class BoardTask extends BaseModel
{
    protected $fillable = [
        'board_id',
        'task_id'
    ];

    protected $table = 'board_task';
}
