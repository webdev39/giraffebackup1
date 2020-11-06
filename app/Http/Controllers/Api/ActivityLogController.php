<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogCollectionResource;

class ActivityLogController extends Controller
{
    /**
     * @var \App\Services\ActivityLog\ActivityLogService;
     */
    private $activityLogService;

    /**
     * ActivityController constructor.
     */
    public function __construct()
    {
        $this->activityLogService = app('ActivityLogSer');
    }

    /**
     * @param int $taskId
     *
     * @return ActivityLogCollectionResource
     */
    public function getActivityLogByTask(int $taskId)
    {
        $activityLogs = $this->activityLogService->getActivityLogByTaskId($taskId);

        return new ActivityLogCollectionResource($activityLogs);
    }

    /**
     * @param int $boardId
     *
     * @return ActivityLogCollectionResource
     */
    public function getActivityLogByBoard(int $boardId)
    {
        $activityLogs = $this->activityLogService->getActivityLogByBoardId($boardId);

        return new ActivityLogCollectionResource($activityLogs);
    }

    /**
     * @param int $groupId
     *
     * @return ActivityLogCollectionResource
     */
    public function getActivityLogByGroup(int $groupId)
    {
        $activityLogs = $this->activityLogService->getActivityLogByGroupId($groupId);

        return new ActivityLogCollectionResource($activityLogs);
    }

    /**
     * @return ActivityLogCollectionResource
     */
    public function getUserActivityLog()
    {
        $activityLogs = $this->activityLogService->getUserActivityLog();

        return new ActivityLogCollectionResource($activityLogs);
    }
}
