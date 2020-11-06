<?php

namespace App\Services\UserTask;

use App\Models\TaskSortOrder;
use App\Services\BaseService;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class UserTaskService
 *
 * @package App\Services\UserTask
 */
class UserTaskService extends BaseService
{
    const TODAY_SLUG    = 'today';
    const WEEK_SLUG     = 'week';

    /** @var array */
    const AVAILABLE_SLUGS = [
        self::TODAY_SLUG => 'endOfDay',
        self::WEEK_SLUG  => 'endOfWeek',
    ];

    /** @var \App\Repositories\UserTenantRepositoryEloquent|\Illuminate\Foundation\Application|mixed */
    private $userTenantRepo;

    /**
     * UserTaskService constructor.
     */
    public function __construct()
    {
        $this->userTenantRepo = app('UserTenantRepo');
    }

    /**
     * @param string $slug
     * @param int    $userTenantId
     * @param bool   $short
     *
     * @return Collection
     */
    public function getUserTaskActivity(string $slug, int $userTenantId, $short = false) : Collection
    {
        $user = $this->userTenantRepo->find($userTenantId)->user;
        $deadline   = self::getDeadline(self::AVAILABLE_SLUGS[$slug], $user->utc_offset);
        $tasks      = app('TaskRepo')->getTasksBeforeDeadline($deadline, $userTenantId, $slug);

        if ($short) {
            return $tasks->map(function($task) {
                return [
                    'task_id'       => $task->id,
                    'sort_order'    => $task->sort_order,
                ];
            });
        }

        return $tasks;
    }

    /**
     * @param string $slug
     * @param int    $userTenantId
     *
     * @return Collection
     */
    public function getUserTaskWithRelationsActivity(string $slug, int $userTenantId)
    {
        $tasks = $this->getUserTaskActivity($slug, $userTenantId);

        return app('TaskSer')->addTaskRelations($tasks);
    }

    /**
     * @param int $userTenantId
     *
     * @return array
     */
    public function getUserCountTaskActivity(int $userTenantId)
    {
        $user = $this->userTenantRepo->find($userTenantId)->user;
        $deadline    = self::getDeadline(self::AVAILABLE_SLUGS[self::WEEK_SLUG], $user->utc_offset);
        $tasks       = app('TaskRepo')->getTasksBeforeDeadline($deadline, $userTenantId);

        $countTask   = [];

        foreach (self::AVAILABLE_SLUGS as $slug => $methodName) {
            $deadline = self::getDeadline($methodName, $user->utc_offset);

            $countTask[$slug] = $tasks->filter(function ($task) use ($deadline) {
                return ($task->planned_deadline) || $task->planned_deadline < $deadline->toDateTimeString();
            })->count();
        }

        return $countTask;
    }

    public static function getDeadline(string $methodName, int $utcOffset = null):?Carbon
    {
        return TimeService::toUserLocalTime(now()->{$methodName}(), $utcOffset);
    }
}
