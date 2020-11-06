<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Permission;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTenantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Method method for check user access
     * @param User $user
     * @param Comment $comment
     * @return mixed
     */
    public function getAccess(User $user, Comment $comment)
    {
        $userTenant =  $user->getChosenTenant();
        $group = $comment->group()->first();
        $groupId = $group->id ?? null;
        return app('UserTenantGroupRepo')->findWhere(
            [
                'user_tenant_id' => $userTenant->id,
                'group_id'       => $groupId
            ]
        )->first();
    }

    public function update(User $user, Comment $comment, Task $task)
    {
        $userTenant = $user->getChosenTenant();
        $task = app('TaskSer')->getTaskDetailsById($comment->task_id);
        if (!$task) {
            return response()->json(['message' => 'Task is not found'], 404);
        }
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User is not a member af the group'], 404);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION]
        );
        return true;
    }

    /**
     * @deprecated
     * @param User $user
     * @param Comment $comment
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function delete_(User $user, Comment $comment)
    {
        $userTenant = $user->getChosenTenant();

        if ($comment->user_id == $userTenant->user_id) {
            return true;
        }
        $task = app('TaskSer')->getTaskDetailsById($comment->task_id);
        if (!$task) {
            return response()->json(['message' => 'Task is not found'], 404);
        }
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_id);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User is not a member af the group'], 404);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION]
        );
        return true;
    }
}
