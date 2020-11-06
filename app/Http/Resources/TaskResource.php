<?php

namespace App\Http\Resources;

use App\Models\TaskSortOrder;
use App\Services\Timer\TimerService;
use Illuminate\Support\Collection;

/**
 * Class TaskResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property string     $name
 * @property string     $description
 * @property int        $priority_id
 * @property int        $budget_id
 * @property int        $budget_type_id
 * @property string     $soft_budget
 * @property string     $hard_budget
 * @property Collection $timers
 * @property Collection $task_subscribers
 * @property Collection $notify_subscribers
 * @property Collection $sub_tasks
 * @property int        $board_id
 * @property string     $board_name
 * @property int        $group_id
 * @property string     $group_name
 * @property string     $deadline
 * @property string     $planned_deadline
 * @property int        $done_by
 * @property int        $is_archive
 * @property int        $draft
 * @property array      $sort_order
 * @property string     $repeat_unit
 * @property int        $repeat_interval
 * @property int        $parent_id
 * @property \Carbon\Carbon|null    $repeat_started_at
 * @property \Carbon\Carbon|null    $repeat_ended_at
 * @property \Carbon\Carbon|null    $created_at
 * @property mixed sort_weight
 */
class TaskResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        $unreadNotificationsCount = app('TaskSer')->getTaskUnreadNotificationsByUser($this->id, \Auth::user())->count();

        $result = [
            'id'                => $this->id,
            'parent_id'         => $this->parent_id,
            'name'              => $this->name,
            'soft_budget'       => $this->soft_budget,
            'hard_budget'       => $this->hard_budget,
            'budget_type_id'    => $this->budget_type_id,
            'deadline'          => $this->deadline,
            'done_by'           => (bool) $this->done_by,
            'is_archive'        => (bool) $this->is_archive,
            'draft'             => self::isDraftTask($this->draft),
            'sort_order'        => self::getSortOrder($this->sort_order ?? []),
            'sort_weight'       => $this->sort_weight,
            'priority_id'       => self::getPriorityId($this->priority_id ?? null, $this->board_id),
            'planned_deadline'  => $this->planned_deadline ?? null,
            'board_id'          => $this->board_id,
            'group_id'          => $this->group_id,
            'created_at'        => $this->created_at,
            'repeat_unit'       => $this->repeat_unit,
            'repeat_interval'   => $this->repeat_interval,
            'repeat_started_at' => $this->repeat_started_at,
            'repeat_ended_at'   => $this->repeat_ended_at,
            'creator_id'        => $this->creator_id,
            'unreadNotificationsCount' => $unreadNotificationsCount
        ];

        if ($request->get('task_res') != 'short') {
            if (isset($this->timers)) {
                $result['logged_time'] = TimerService::calcTaskSpentTime($this->timers);
                $result['tracked_time'] = $this->getLoggedTimeByUserTenantId();
            }

            if (isset($this->task_subscribers) && isset($this->timers)) {
                $result['subscribers']['task'] = $this->getCollectionColumn($this->task_subscribers, 'id');
            }

            if (isset($this->notify_subscribers)) {
                $result['subscribers']['notify'] = $this->getCollectionColumn($this->notify_subscribers, 'id');
            }

            if (isset($this->count)) {
                $result['count'] = $this->count;
            }
        }

        if ($request->get('task_res') == 'long') {
            $result['description']       = $this->description;
            $result['board_name']        = $this->board_name;
            $result['group_name']        = $this->group_name;
            $result['budget_id']         = $this->budget_id         ?? null;

            if (isset($this->sub_tasks)) {
                $result['sub_tasks'] = SubTaskResource::collection($this->sub_tasks);
            } else {
                $result['sub_tasks'] = [];
            }
        }

        return $result;
    }

    /**
     * @param $draft
     *
     * @return int|null
     */
    private static function isDraftTask($draft) : ?int
    {
        return $draft === 0 ? null : $draft;
    }

    /**
     * @return array|\stdClass
     * @throws \Exception
     */
    private function getLoggedTimeByUserTenantId()
    {
        $timers = $this->timers->groupBy('user_tenant_id');
        $loggedTime = [];

        /** @var Collection $userTimers */
        foreach ($timers as $userTenantId => $userTimers) {
            if ($userTimers->count()) {
                $lastTimer  = $userTimers->last(function ($timer) {
                    return $timer->updated_at;
                });

                $loggedTime[$userTenantId] = [
                    'time'          => TimerService::calcTaskSpentTime($userTimers),
                    'activity_at'   => $lastTimer->updated_at,
                ];
            }
        }

        return count($loggedTime) ? $loggedTime : null;
    }

    /**
     * @param array $sortOrder
     *
     * @return array
     */
    private static function getSortOrder($sortOrder = [])
    {
        foreach (TaskSortOrder::SORT_ORDER_TYPES as $key => $value) {
            if (is_array($sortOrder) && !array_key_exists($key, $sortOrder)) {
                $sortOrder[$key] = null;
            }
        }

        return $sortOrder;
    }

    private static function getPriorityId($priorityId, $boardId)
    {
        if ($priorityId) {
            return $priorityId;
        }

        return optional(app('PrioritySer')->getPrimaryPriorityByBoardId($boardId))->id;
    }
}
