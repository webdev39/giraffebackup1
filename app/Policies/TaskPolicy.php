<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class TaskPolicy
{
    use HandlesAuthorization;

    public function getAccess(User $user, Task $task)
    {
        $group = $task->board->first()->group;
        $userTenant =  $user->getChosenTenant();
        return app('UserTenantGroupRepo')->findWhere(
            [
                'user_tenant_id' => $userTenant->id,
                'group_id'       => $group->id
            ]
        );
    }

    public function userAssignedToTask(User $user, Task $task)
    {
        $userTenant = $user->getChosenTenant();
        return $task->taskSubscribers->contains('userTenantId', $userTenant->id);
    }

    public function changeWorkflow(User $user, Task $task)
    {
        $group = $task->board->first()->group;
        if (!$group) {
            abort(404, 'Group is not found');
        }
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }
        return true;
    }

    public function delete(User $user, Task $task)
    {
        $group = $task->board->first()->group;
        if (!$group) {
            abort(404, 'Group is not found');
        }
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::DELETE_TASK_PERMISSION]
        );
    }

    public function readComments(User $user, Task $task)
    {

        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            abort( 403, 'User is not a member af the group');
        }
        if ($user->getChosenTenant()->can(Permission::READ_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        if ($userTenantGroup->can(Permission::READ_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        return false;
    }

    /**
     * @deprecated
     * @param User $user
     * @param Task $task
     * @param UserTenant $userTenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComment_(User $user, Task $task, UserTenant $userTenant)
    {
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            return response()->json(['message' => 'User is not a member af the group'], 404);
        }
        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::READ_TASK_PERMISSION]
        );
    }

    public function readTimeLogs(User $user, Task $task)
    {
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            abort( 403, 'User is not a member af the group');
        }
        if ($user->getChosenTenant()->can(Permission::READ_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        if ($userTenantGroup->can(Permission::READ_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        return false;
    }

    public function logTime(User $user, Task $task)
    {
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            abort( 403, 'User is not a member af the group');
        }

        if ($user->getChosenTenant()->can(Permission::TIME_TRACKING_PERMISSION['name'])) {
            return true;
        }
        if ($userTenantGroup->can(Permission::TIME_TRACKING_PERMISSION['name'])) {
            return true;
        }

        return false;
/*        Auth::failIfHasNoPermissionsOnTenantLevel(
            $user->getChosenTenant(),
            $userTenantGroup,
            [Permission::TIME_TRACKING_PERMISSION]
        );*/
    }
}
