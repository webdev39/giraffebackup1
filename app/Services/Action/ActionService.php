<?php

namespace App\Services\Action;

use App\Models\Permission;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use App\Repositories\ActionRepositoryEloquent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActionService
{
    /** @var \App\Services\ActivityLog\ActivityLogService|\Illuminate\Foundation\Application|mixed */
    private $activityLogService;

    /** @var \App\Services\Comment\CommentService|\Illuminate\Foundation\Application|mixed */
    private $commentService;

    /** @var \App\Services\Log\TimerLogService|\Illuminate\Foundation\Application|mixed */
    private $timerLogService;

    /** @var ActionRepositoryEloquent */
    private $actionRepo;

    /**
     * ActionService constructor.
     */
    public function __construct()
    {
        $this->actionRepo           = app('ActionRepo');

        $this->activityLogService   = app('ActivityLogSer');
        $this->commentService       = app('CommentSer');
        $this->timerLogService      = app('TimerLogSer');
    }

    /**
     * @param Collection $actions
     *
     * @return Collection
     */
    private function setActionRelations(Collection $actions) : Collection
    {
        $activityLog    = $actions->where('source', 'activity_log');
        $timerLog       = $actions->where('source', 'timer_log');
        $comments       = $actions->where('source', 'comment');

        $activityLog    = $this->activityLogService->addActivityLogRelations($activityLog);
        $comments       = $this->commentService->addCommentRelations($comments, true);
        $timerLog       = $this->timerLogService->addTimerLogRelations($timerLog);

        /** @var Collection $result */
        $result = collect()->merge($comments)->merge($timerLog)->merge($activityLog);

        return $result->sortByDesc(function ($obj) {
            return $obj->created_at;
        })->values();
    }

    /**
     * @param int   $taskId
     * @param array $options
     * @param int   $page
     * @param int   $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getActionByTaskId(int $taskId, array $options, $page = 1, $perPage = 30)
    {
        /** @var Collection $actions */
        $actions = $this->actionRepo->getActionByTaskId($taskId, $options, $page, $perPage);
        $actions = $this->setActionRelations($actions);

        return new LengthAwarePaginator($actions->all(), $actions->count(), $perPage, $page);
    }

    /**
     * @param UserTenant $userTenant
     * @param UserTenantGroup $userTenantGroup
     * @param int $taskId
     * @param array $options
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActionByTaskIdForTenantByGroup(UserTenant $userTenant, UserTenantGroup $userTenantGroup , int $taskId, array $options, $page = 1, $perPage = 30)
    {
        /** @var Collection $actions */
        $actions = $this->actionRepo->getActionByTaskId($taskId, $options, $page, $perPage);
        $actions = $this->setActionRelations($actions);

        if(!$userTenant->can(Permission::READ_OTHER_COMMENTS_PERMISSION['name']) && !$userTenantGroup->can(Permission::READ_OTHER_COMMENTS_PERMISSION['name'])) {
            $actions = $actions->filter(function($action) use($userTenant) {
                return $action->source != 'comment' || ($action->source == 'comment' && $action->user->id == $userTenant->user_id);
            });
        }

        $permissions = app('PermissionSer')->getAllUserPermissions($userTenant->user, $userTenant);
        $hasTimeTrackingPermission = $permissions->filter(static function($permission) {
            return $permission->name === Permission::TIME_TRACKING_PERMISSION['name'];
        })->count();

        if(!$userTenant->can(Permission::READ_TIME_LOGS_PERMISSION['name']) && !$userTenantGroup->can(Permission::READ_TIME_LOGS_PERMISSION['name'])) {
            $actions = $actions->filter(function($action) use($userTenant) {
                return $action->source != 'timer_log' || ($action->source == 'timer_log' && $action->user->id == $userTenant->user_id);
            });
        }
        if(!$hasTimeTrackingPermission) {
            $actions = $actions->filter(function($action) use($userTenant) {
                if($action->source == 'timer_log') {
                    return $action->timer->user_tenant_id === $userTenant->id;
                } else {
                    return $action;
                }
            });
        }

        return new LengthAwarePaginator($actions->all(), $actions->count(), $perPage, $page);
    }

    /**
     * @param int   $boardId
     * @param array $options
     * @param int   $page
     * @param int   $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getActionByBoardId(int $boardId, array $options, $page = 1, $perPage = 30)
    {
        /** @var Collection $actions */
        $actions = $this->actionRepo->getActionByBoardId($boardId, $options, $page, $perPage);
        $actions = $this->setActionRelations($actions);

        return new LengthAwarePaginator($actions->all(), $actions->count(), $perPage, $page);
    }

    /**
     * @param int   $groupId
     * @param array $options
     * @param int   $page
     * @param int   $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getActionByGroupId(int $groupId, array $options, $page = 1, $perPage = 30)
    {
        /** @var Collection $actions */
        $actions = $this->actionRepo->getActionByGroupId($groupId, $options, $page, $perPage);
        $actions = $this->setActionRelations($actions);

        return new LengthAwarePaginator($actions->all(), $actions->count(), $perPage, $page);
    }
}
