<?php

namespace App\Models;

/**
 * App\Models\Filter
 *
 * @property int $id
 * @property int $user_tenant_id
 * @property string $name
 * @property string|null $range
 * @property array $assigner_ids
 * @property array $group_ids
 * @property array $board_ids
 * @property array $priority_ids
 * @property int|null $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $assigners
 * @property-read mixed $boards
 * @property-read mixed $groups
 * @property-read mixed $priorities
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereAssignerIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereBoardIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereGroupIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter wherePriorityIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereUserTenantId($value)
 * @mixin \Eloquent
 * @property int $view_type_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filter whereViewTypeId($value)
 */
class Filter extends BaseModel
{
    const TODAY = 'today';
    const YESTERDAY = 'yesterday';
    const TOMORROW = 'tomorrow';
    const NEXT_WEEK = 'next_week';
    const THIS_WEEK = 'this_week';
    const LAST_WEEK = 'last_week';
    const THIS_MONTH = 'this_month';
    const LAST_MONTH = 'last_month';

    protected $fillable = [
        'user_tenant_id',
        'name',
        'range',
        'assigner_ids',
        'group_ids',
        'board_ids',
        'priority_ids',
        'view_type_id',
        'status'
    ];

    protected $table = 'filters';


    protected $casts = [
        'assigner_ids' => 'array',
        'group_ids' => 'array',
        'board_ids' => 'array',
        'priority_ids' => 'array',
    ];

    protected $appends = ['assigners', 'groups', 'boards', 'priorities'];

    public function getAssignersAttribute()
    {
        return $this->assigner_ids ? app('UserTenantRepo')->with('user')->findWhereIn('id', $this->assigner_ids) : null;
    }

    public function getGroupsAttribute()
    {
        return $this->group_ids ? app('GroupRepo')->findWhereIn('id', $this->group_ids) : null;
    }

    public function getBoardsAttribute()
    {
        return $this->board_ids ? app('BoardRepo')->findWhereIn('id', $this->board_ids) : null;
    }

    public function getPrioritiesAttribute()
    {
        return $this->priority_ids ? app('PriorityRepo')->findWhereIn('id', $this->priority_ids) : null;
    }

//    public function getRangeTypeAttribute()
//    {
//        $now = Carbon::now();
//        switch ($this->range) {
//            case null:
//                return null;
//            case self::TODAY:
//                return $now->toDateString() . '/' . $now->toDateString();
//            case self::YESTERDAY:
//                return $now->subDay()->toDateString() . '/' . $now->subDay()->toDateString();
//            case self::TOMORROW:
//                return $now->addDay()->toDateString() . '/' . $now->addDay()->toDateString();
//            case self::NEXT_WEEK:
//                $from = $now->endOfWeek();
//                return $from->toDateString() . '/' . $from->addWeek()->toDateString();
//            case self::LAST_WEEK:
//                $to = $now->startOfWeek();
//                return $to->subWeek()->toDateString() . '/' . $to->toDateString();
//            case self::THIS_WEEK:
//                return $now->startOfWeek()->toDateString() . '/' . $now->endOfWeek()->toDateString();
//            case self::THIS_MONTH:
//                return $now->startOfMonth()->toDateString() . '/' . $now->endOfMonth()->toDateString();
//            case self::LAST_MONTH:
//                $to = $now->startOfMonth();
//                return $to->subMonth()->toDateString() . '/' . $to->toDateString();
//            default:
//                return $this->range;
//        }
//    }
}
