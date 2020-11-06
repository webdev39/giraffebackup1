<?php

namespace App\Models;

use App\Services\UserTask\UserTaskService;

/**
 * App\Models\TaskSortOrder
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $task_id
 * @property string $type
 * @property int|null $model_id
 * @property int|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskSortOrder query()
 */
class TaskSortOrder extends BaseModel
{
    /**
     * List sort order types
     */
    const SORT_ORDER_TYPES = [
        'list' => [
            'name'      => 'list',
            'private'   => false,
        ],
        'kanban' => [
            'name'      => 'kanban',
            'private'   => false,
        ],
        'filter' => [
            'name'      => 'filter',
            'model'     => 'filter',
            'private'   => true,
        ],
        'today' => [
            'name'      => UserTaskService::TODAY_SLUG,
            'private'   => true,
        ],
        'week' => [
            'name'      => UserTaskService::WEEK_SLUG,
            'private'   => true,
        ],
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_sort_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'type',
    ];
}