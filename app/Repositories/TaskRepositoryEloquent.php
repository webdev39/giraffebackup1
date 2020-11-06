<?php

namespace App\Repositories;

use App\Events\Eloquent\Saved\CreatedTaskEvent;
use App\Models\Board;
use App\Models\NotificationSubscription;
use App\Models\Task;
use App\Models\TaskSortOrder;
use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Class TaskRepositoryEloquent
 * @package App\Repositories
 *
 * @property Task $model
 */
class TaskRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Task::class;
    }

    /**
     * @param int|null $userTenantId
     *
     * @return Builder
     */
    public function buildTaskWithRelations(int $userTenantId = null)
    {
        $query = DB::table($this->taskTable)
            ->select([
                $this->taskTable.'.id',
                $this->taskTable.'.parent_id',
                $this->taskTable.'.name',
                $this->taskTable.'.description',
                $this->taskTable.'.priority_id',
                $this->taskTable.'.is_archive',
                $this->taskTable.'.deadline',
                $this->taskTable.'.draft',
                $this->taskTable.'.done_by',
                $this->taskTable.'.updated_at',
                $this->taskTable.'.sort_weight',
                $this->taskTable.'.created_at',
                $this->taskTable.'.creator_id',
                $this->boardTable.'.id                  as board_id',
                $this->boardTable.'.name                as board_name',
                $this->groupTable.'.id                  as group_id',
                $this->groupTable.'.name                as group_name',
                $this->budgetTable.'.id                 as budget_id',
                $this->budgetTable.'.soft_budget        as soft_budget',
                $this->budgetTable.'.hard_budget        as hard_budget',
                $this->budgetTable.'.budget_type_id     as budget_type_id',
                $this->budgetTable.'.budget_type_id     as budget_type_id',
                $this->repeatTaskTable.'.time_unit      as repeat_unit',
                $this->repeatTaskTable.'.time_interval  as repeat_interval',
                $this->repeatTaskTable.'.started_at     as repeat_started_at',
                $this->repeatTaskTable.'.ended_at       as repeat_ended_at',
            ])
            ->leftJoin($this->budgetTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->budgetTable.'.id', $this->taskTable.'.budget_id');
            })
            ->leftJoin($this->repeatTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->repeatTaskTable.'.task_id', $this->taskTable.'.id');
            })
            ->join($this->boardTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->boardTaskTable.'.task_id', $this->taskTable.'.id');
            })
            ->join($this->boardTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->boardTable.'.id', $this->boardTaskTable.'.board_id');
            })
            ->join($this->groupTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->groupTable.'.id', $this->boardTable.'.group_id');
            })
            ->where($this->taskTable.'.deleted_at', null);
        
        if ($userTenantId) {
            $query->addSelect([
                $this->personalDeadlineTable.'.planned_deadline as planned_deadline'
            ])->leftJoin($this->personalDeadlineTable, function ($join) use ($userTenantId) {
                /** @var JoinClause $join */
                $join->on($this->personalDeadlineTable.'.task_id', $this->taskTable.'.id')
                    ->where($this->personalDeadlineTable.'.user_tenant_id', $userTenantId);
            });
        }

        return $query;
    }

    /**
     * @param int|null $userTenantId
     *
     * @return Builder
     */
    public function buildCreatedTaskWithRelations(int $userTenantId = null)
    {
        return $this->buildTaskWithRelations($userTenantId)
            ->where(function ($query) use ($userTenantId) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where($this->taskTable.'.draft', 0)
                    ->orWhere($this->taskTable.'.draft', $userTenantId);
            });
    }

    /**
     * @param array    $boardIds
     * @param int|null $userTenantId
     *
     * @return Collection
     */
    public function getTasksByBoardIds(array $boardIds, int $userTenantId = null) : Collection
    {
        return $this->buildCreatedTaskWithRelations($userTenantId)
            ->whereIn($this->boardTable.'.id', $boardIds)
            ->get()
            ->unique('id');
    }

    /**
     * @param array $boardIds
     * @param int|null $userTenantId
     *
     * @param bool $hide_done
     * @return Collection
     */
    public function getActiveTasksByBoardId(array $boardIds, int $userTenantId = null, bool $hide_done) : Collection
    {
        $taskList = $this->buildCreatedTaskWithRelations($userTenantId)
            ->whereIn($this->boardTable.'.id', $boardIds);

        if ($hide_done) {
            $taskList->whereNull($this->taskTable.'.done_by');
        }

        return $taskList->get()
            ->unique('id');
    }

    /**
     * @param array    $boardIds
     *
     * @return Collection
     */
    public function getCountTasksByBoardIds(array $boardIds) : Collection
    {
        return Board::select('id', 'name')
            ->whereIn('id', $boardIds)
            ->with(["tasks" => function($query){
                $query->select('tasks.id');
            }])
            ->get();
    }

    /**
     * @param array    $boardIds
     * @param int|null $userTenantId
     *
     * @return Collection
     */
    public function getActiveTasksByBoardIds(array $boardIds, int $userTenantId = null) : Collection
    {
        return $this->buildCreatedTaskWithRelations($userTenantId)
            ->whereIn($this->boardTable.'.id', $boardIds)
            ->where($this->taskTable.'.is_archive',0)
            ->get()
            ->unique('id');
    }

    /**
     * @param array    $taskIds
     * @param int|null $userTenantId
     *
     * @return Collection
     */
    public function getTasksByIds(array $taskIds, int $userTenantId = null)
    {
        return $this->buildTaskWithRelations($userTenantId)
            ->whereIn($this->taskTable.'.id', $taskIds)
            ->orderBy('sort_weight', 'asc')
            ->get();
    }

    /**
     * @param int $userTenantId
     * @param int $lastCount
     *
     * @return Collection
     */
    public function getLatestActiveBoardsByUserTenantId(int $userTenantId, int $lastCount = 5)
    {
        return $this->buildCreatedTaskWithRelations($userTenantId)
            ->addSelect([
                DB::raw("$this->timerTable.updated_at AS timer_updated_at")
             ])
            ->join($this->timerTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->timerTable.'.updated_at',
                    DB::raw("(
                        SELECT max($this->timerTable.updated_at)
                        FROM $this->timerTable
                        WHERE $this->timerTable.task_id = $this->taskTable.id
                    )")
                );
            })
            ->where($this->timerTable.'.user_tenant_id', $userTenantId)
            ->where($this->boardTable.'.is_archive',0)
            ->whereNull($this->taskTable.'.done_by')
            ->latest('timer_updated_at')
            ->limit($lastCount)
            ->get();
    }

    /**
     * @param string      $deadline
     * @param int         $userTenantId
     * @param string|null $slug
     *
     * @return Collection
     */
    public function getTasksBeforeDeadline(string $deadline, int $userTenantId, string $slug = null) : Collection
    {
        $query = $this->buildCreatedTaskWithRelations($userTenantId)
            ->join($this->userTenantTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTaskTable.'.task_id', '=', $this->taskTable.'.id');
            })
            ->where($this->userTenantTaskTable.'.user_tenant_id', $userTenantId)
            ->where($this->taskTable.'.draft', 0)
            ->where($this->taskTable.'.is_archive', 0)
            ->where($this->boardTable.'.is_archive', 0)
            ->where($this->taskTable.'.done_by', null)
            ->where(function ($query) use ($deadline) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query
                    ->where($this->personalDeadlineTable.'.planned_deadline', '<=', $deadline)
                    ->orWhere($this->personalDeadlineTable.'.planned_deadline', null);
            });

        if ($slug) {
            $query = $query
                ->addSelect([
                    $this->userTaskSortOrderTable.'.order as sort_order',
                ])
                ->leftJoin($this->userTaskSortOrderTable, function ($join) use ($slug, $userTenantId) {
                    /** @var JoinClause $join */
                    $join->on($this->userTaskSortOrderTable.'.task_id',  $this->taskTable.'.id')
                        ->where($this->userTaskSortOrderTable.'.user_id',  $userTenantId)
                        ->where($this->userTaskSortOrderTable.'.type',  TaskSortOrder::SORT_ORDER_TYPES[$slug]['name']);
                });
        }

        return $query->get();
    }

    /**
     * @return Collection
     */
    public function getDoneTasksByMonthlyBudget(): Collection
    {
        return $this->buildTaskWithRelations()
            ->join($this->budgetTypesTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->budgetTypesTable.'.id', $this->budgetTable.'.budget_type_id');
            })
            ->where($this->budgetTypesTable.'.name', 'Monthly')
            ->where($this->taskTable.'.done_by', '!=', null)
            ->get();
    }

    /**
     * @param int  $taskId
     * @param bool $isDone
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function changeTaskWorkflow(int $taskId, bool $isDone)
    {
        return $this->update([
            'done_by' => $isDone ? Auth::userTenantId() : null
        ], $taskId);
    }

    /**
     * @param array $taskIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveTaskByIds(array $taskIds, bool $isArchive = false)
    {
        DB::transaction(function () use ($taskIds, $isArchive) {
            $tasks = $this->model->findMany($taskIds);
            $tasks->each(function ($task) use ($isArchive) {
                /** @var Task $task */
                $task->is_archive = $isArchive ? 1 : 0;
                $task->save();
            });
        });
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getTaskSubscribersByTaskIds(array $taskIds) : Collection
    {
        return DB::table($this->userTenantTable)
            ->select([
                $this->userTenantTable.'.*',
                $this->userTenantTaskTable.'.task_id            as task_id',
                $this->userTenantTaskTable.'.user_tenant_id     as user_tenant_id',
                $this->userTenantTaskTable.'.id                 as user_tenant_task_id',
            ])
            ->join($this->userTenantTaskTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTaskTable.'.user_tenant_id', '=', $this->userTenantTable.'.id');
            })
            ->whereIn($this->userTenantTaskTable.'.task_id', $taskIds)
            ->get();
    }

    /**
     * @param int   $taskId
     * @param array $attributes
     *
     * @return mixed
     */
    public function updateBudget(int $taskId, array $attributes = [])
    {
        return $this->find($taskId)->budget()->update($attributes);
    }

    /**
     * @param array $taskIds
     * @param int   $priorityId
     *
     * @return bool
     */
    public function updatePriority(array $taskIds, int $priorityId)
    {
        return $this->model->whereIn('id', $taskIds)->update(['priority_id' => $priorityId]);
    }

    /**
     * @param string $field
     * @param array  $attributes
     * @param array  $value
     *
     * @return bool
     */
    public function updateWhereIn(string $field, array $attributes, array $value = [])
    {
        return $this->model->whereIn($field, $attributes)->update($value);
    }

    /**
     * @param int $boardId
     * @param int $userTenantId
     *
     * @return null
     */
    public function getDraftTask(int $boardId, int $userTenantId)
    {
        $draftTask = $this->buildTaskWithRelations()
            ->where($this->boardTaskTable.'.board_id', $boardId)
            ->where($this->taskTable.'.draft', $userTenantId)
            ->first();

        if ($draftTask) {
            return $this->model->withoutGlobalScopes()->find($draftTask->id);
        }

        return null;
    }

    /**
     * @param array $attributes
     * @param int    $draftTaskId
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     * @throws \Throwable
     */
    public function draftTaskToTask(array $attributes, int $draftTaskId)
    {
        $draftTask = $this->getDraftTaskModelById($draftTaskId);

        if (!$draftTask) {
            abort(404, 'Task not found');
        }

        DB::transaction(function () use ($draftTask, $attributes) {
            $draftTask->name = $attributes['name'];
            $draftTask->priority_id = $attributes['priority_id'];
            $draftTask->draft = 0;
            $draftTask->creator_id = Auth::id();
            $draftTask->created_at = now();
            $draftTask->parent_id = $attributes['parent_id'];
            $draftTask->save();


            event(new CreatedTaskEvent($draftTask));

            $draftTask->notifySubscriptions()->first()->touch();

            foreach ($draftTask->userTenantTask as $subscriber) {
                $subscriber->touch();
            }
        });

        return $draftTask;
    }

    /**
     * @param int $draftTaskId
     *
     * @return mixed
     */
    public function getDraftTaskModelById(int $draftTaskId)
    {
        return $this->model->withoutGlobalScopes()->find($draftTaskId);
    }

    /**
     * @param array $taskIds
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeTaskByIds(array $taskIds)
    {
        return $this->model->withoutGlobalScopes()->whereIn('id', $taskIds)->delete();
    }

    /**
     * @return Collection
     */
    public function getDraftTasks() : Collection
    {
        return $this->model->withoutGlobalScopes()->where('draft', '!=', false)->get();
    }

    /**
     * @param UserTenant $userTenant
     * @param $query
     * @return Task|EloquentBuilder
     */
    public function searchForTenantUser(UserTenant $userTenant, $query)
    {
        $accessibleGroupIds = $userTenant->groups->pluck('id');

        return $this->model->whereIn('id', $this->model->search($query)->get()->pluck('id'))
            ->whereHas('board', function (EloquentBuilder $builder) use ($accessibleGroupIds) {
                    return $builder->where('board_id', '>', 0)->whereIn('group_id', $accessibleGroupIds);
            })
         /*   ->whereHas('userTenantTask', function(EloquentBuilder $builder) use ($userTenant) {
                return $builder->where('user_tenant_id', $userTenant->id);
            })*/;
    }

    /**
     * @return Task
     */
    public function getTaskHaveOverDeadline(): Collection
    {
        $options = ['id', 'name', 'creator_id', 'deadline', 'done_by', 'draft'];

        $tasks = $this->model->withoutGlobalScopes()
            ->select($options)
            ->where([
                ['deleted_at', null],
                ['deadline', '<', Carbon::now()->toDateTimeString()],
                ['done_by', null],
                ['draft', 0]
            ])
            ->with(['taskSubscribers.user'])
            ->get();

        return $tasks;
    }

    /**
     * @return Task
     */
    public function getTaskHaveOverBudget()
    {
        $options = ['id', 'name', 'creator_id', 'budget_id', 'done_by', 'draft'];

        $tasks = $this->model->withoutGlobalScopes()
            ->select($options)
            ->where([
                ['deleted_at', null],
                ['done_by', null],
                ['draft', 0]
            ])
            ->with([
                'taskSubscribers.user',
                'budget'
            ])
            ->get();

        return $tasks;
    }

}
