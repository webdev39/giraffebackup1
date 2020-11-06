<?php

namespace App\Http\Resources;

use App\Services\Budget\BudgetService;
use App\Services\Timer\TimerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class BoardResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property string     $name
 * @property bool       $is_archive
 * @property int        $view_type_id
 * @property Collection $tasks
 * @property string     $description
 * @property string     $deadline
 * @property int        $budget_id
 * @property int        $budget_type_id
 * @property string     $soft_budget
 * @property string     $hard_budget
 * @property int        $group_id
 * @property string     $group_name
 */
class BoardResource extends BaseResource
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

        $result = [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'deadline'              => $this->deadline,
            'view_type_id'          => $this->view_type_id,
            'group_id'              => $this->group_id,
            'is_archive'            => (bool) $this->is_archive,
            'hide_done_tasks'       => (bool) $this->hide_done_tasks,
            'creator_id'            => $this->creator_id,
            'quick_nav'             => $this->quick_nav,
        ];

        if (isset($this->tasks)) {
            $result['budget'] = BudgetService::calcBoardBudget($this->tasks);
        }

        if ($request->get('board_res') != 'short') {
            if (isset($this->tasks)) {
                $timers = $this->tasks->pluck('timers')->collapse();

                $result['tasks']                 = TaskResource::collection($this->tasks);
                $result['billedTime']            = TimerService::calcTaskBilledTime($timers);
                $result['trackedTime']           = TimerService::calcTaskSpentTime($timers);
                $result['calendar_opened_tasks'] = $this->getCalendarOpenedTasks();
                $result['calendar_closed_tasks'] = $this->getCalendarClosedTasks();
            }

            if (isset($this->tasks_count)) {
                $result['tasks_count'] = $this->tasks_count;
            }
        }

        if ($request->get('board_res') == 'long') {
            $result = array_merge($result, [
                'description'       => $this->description,
                'budget_id'         => $this->budget_id,
                'budget_type_id'    => $this->budget_type_id,
                'soft_budget'       => $this->soft_budget,
                'hard_budget'       => $this->hard_budget,
                'group_name'        => $this->group_name,
            ]);
        }

        return $result;
    }

    /**
     * @param int $period
     *
     * @return Collection
     */
    public function getCalendarOpenedTasks(int $period = 30)
    {
        if ($this->tasks) {
            $start = Carbon::today()->subDays($period);

            return $this->tasks
                ->where('draft', 0)
                ->where('created_at', '>', $start)
                ->groupBy(function($task) {
                    return Carbon::parse($task->created_at)->toDateString();
                })->map(function($tasks) {
                    /** @var Collection $tasks*/
                    return $tasks->count();
                });
        }

        return null;
    }

    /**
     * @param int $period
     *
     * @return array
     */
    public function getCalendarClosedTasks(int $period = 30)
    {
        if ($this->tasks) {
            $start = Carbon::today()->subDays($period);

            return $this->tasks
                ->where('draft', 0)
                ->where('done_by', '!=', null)
                ->where('updated_at', '>', $start)
                ->groupBy(function($task) {
                    return Carbon::parse($task->created_at)->toDateString();
                })->map(function($tasks) {
                    /** @var Collection $tasks*/
                    return $tasks->count();
                });
        }

        return null;
    }
}
