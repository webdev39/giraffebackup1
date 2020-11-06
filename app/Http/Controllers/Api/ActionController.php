<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActionCollectionResource;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    /** Services */
    /** @var \App\Services\Action\ActionService|\Illuminate\Foundation\Application|mixed  */
    private $actionService;
    private $boardService;
    private $taskService;

    /**
     * ActivityController constructor.
     */
    public function __construct()
    {
        $this->actionService        = app('ActionSer');
        $this->boardService         = app('BoardSer');
        $this->taskService          = app('TaskSer');
    }

    /**
     * @param ActivityRequest $request
     * @param int             $taskId
     *
     * @return ActionCollectionResource
     */
    public function getActionByTask(ActivityRequest $request, int $taskId) : ActionCollectionResource
    {
        $task   = $this->taskService->getTaskById($taskId);
        $board  = $this->boardService->getBoardModelById($task->board_id);

        $userTenant = Auth::userTenant();
        $userTenantGroup = Auth::userTenantGroup($board->group_id);

        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not member of the group');
        }
        $activity = $this->actionService->getActionByTaskIdForTenantByGroup($userTenant, $userTenantGroup, $taskId, $request->all(), request('page'));

        return new ActionCollectionResource($activity);
    }

    /**
     * @param ActivityRequest $request
     * @param int             $boardId
     *
     * @return ActionCollectionResource
     */
    public function getActionByBoard(ActivityRequest $request, int $boardId) : ActionCollectionResource
    {
        $board  = $this->boardService->getBoardModelById($boardId);

        $userTenantGroup = Auth::userTenantGroup($board->group_id);

        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not member of the group');
        }

        $activity = $this->actionService->getActionByBoardId($boardId, $request->all(), request('page'));

        return new ActionCollectionResource($activity);
    }

    /**
     * @param ActivityRequest $request
     * @param int             $groupId
     *
     * @return ActionCollectionResource
     */
    public function getActionByGroup(ActivityRequest $request, int $groupId) : ActionCollectionResource
    {
        $userTenantGroup = Auth::userTenantGroup($groupId);

        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not member of the group');
        }

        $activity = $this->actionService->getActionByGroupId($groupId, $request->all(), request('page'));

        return new ActionCollectionResource($activity);
    }
}
