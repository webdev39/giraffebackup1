<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\UserTask\UserTaskService;
use Illuminate\Support\Facades\Auth;

class UserTaskController extends Controller
{
    /** @var UserTaskService **/
    private $userTaskService;

    /**
     * UserTaskController constructor.
     */
    public function __construct()
    {
        $this->userTaskService = app('UserTaskSer');
    }

    /**
     * @param string $slug
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserTaskActivity(string $slug)
    {
        if (request('deadline_task_res') == 'short') {
            $tasks = $this->userTaskService->getUserTaskActivity($slug, Auth::userTenantId(), true);

            return response()->json([
                'tasks' => $tasks
            ]);
        }

        $tasks = $this->userTaskService->getUserTaskWithRelationsActivity($slug, Auth::userTenantId());

        return response()->json([
            'tasks' => TaskResource::collection($tasks)
        ]);
    }

    /**
     * @return array
     */
    public function getUserTaskDeadline() : array
    {
        $userTenant = Auth::userTenant();
        $result = [];

        foreach (UserTaskService::AVAILABLE_SLUGS as $slug => $days) {
            $result[$slug] = $this->userTaskService->getUserTaskActivity($slug, $userTenant->id, true);
        }

        return $result;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCountTaskActivity()
    {
        $countTask = $this->userTaskService->getUserCountTaskActivity(Auth::userTenantId());

        return response()->json([
            'task_activity' => $countTask
        ]);
    }
}
