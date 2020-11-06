<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\Permission;
use App\Models\Timer;
use App\Models\User;
use App\Models\UserTenantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class LogPolicy
{
    use HandlesAuthorization;

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
        if ($user->getChosenTenant()->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        if ($userTenantGroup->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name'])) {
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
        if ($user->getChosenTenant()->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name'])) {
            return true;
        }
        if ($userTenantGroup->can(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name'])) {
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
