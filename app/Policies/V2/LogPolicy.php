<?php


namespace App\Policies\V2;


use App\Models\Log;
use App\Models\Permission;
use App\Models\Task;
use App\Models\Timer;
use App\Models\User;
use App\Models\UserTenantGroup;
use Illuminate\Support\Facades\Auth;

class LogPolicy extends \App\Policies\LogPolicy
{

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function readTimeLogs(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);

        return ($user->able('READ_TIME_LOGS') || optional($userTenantGroup)->able('READ_TIME_LOGS'))
            && $user->can('getAccess', $task);
    }
    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function create(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);

        return ($user->able('TIME_TRACKING') || optional($userTenantGroup)->able('TIME_TRACKING'))
            && $user->can('getAccess', $task);
    }

    public function removeLog(User $user, Log $log)
    {
        /** @var Timer $timer */
        $timer = $log->timer->first();
        if (!$timer) {
            abort(404, 'Related timer not found');
        }
        if ($timer->user_tenant_id == $user->getChosenTenant()->id) {
            return;
        }
        $task = $timer->task;
        if (!$task) {
            abort(404, 'Related task not found');
        }
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            abort( 403, 'User is not a member af the group');
        }

        if ($user->able('DELETE_TIME_LOGS')) {
            return true;
        }
        if ($userTenantGroup->able('DELETE_TIME_LOGS')) {
            return true;
        }
        return false;
    }


    public function updateLog(User $user, Log $log)
    {
        /** @var Timer $timer */
        $timer = $log->timer->first();
        if (!$timer) {
            abort(404, 'Related timer not found');
        }
        if ($timer->user_tenant_id == $user->getChosenTenant()->id) {
            return;
        }
        $task = $timer->task;
        if (!$task) {
            abort(404, 'Related task not found');
        }
        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = Auth::userTenantGroup($task->group_detail['groupId']);
        if (!$userTenantGroup) {
            abort( 403, 'User is not a member af the group');
        }
        if ($user->able('EDIT_TIME_LOGS')) {
            return true;
        }
        if ($userTenantGroup->able('EDIT_TIME_LOGS')) {
            return true;
        }
        return false;
    }

    public function timeTrack(User $user, Log $log)
    {
        /** @var Timer $timer */
        $timer = $log->timer->first();
        if (!$timer) {
            abort(404, 'Related timer not found');
        }
        if ($timer->user_tenant_id == $user->getChosenTenant()->id) {
            return;
        }
        $task = $timer->task;
        if (!$task) {
            abort(404, 'Related task not found');
        }
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
    }
}