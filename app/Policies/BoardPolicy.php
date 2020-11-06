<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Permission;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class BoardPolicy
{
    use HandlesAuthorization;

    public function getAccess(User $user, Board $board)
    {
        $userTenant =  $user->getChosenTenant();

        return app('UserTenantGroupRepo')->findWhere(
            [
                'user_tenant_id' => $userTenant->id,
                'group_id'       => $board->group->id
            ]
        )->first();
    }

    public function showDoneTask(User $user, Board $board)
    {
        $group = $board->group;
        if (!$group) {
            abort(404, 'Group is not found');
        }
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }
        if ($userTenant->can(Permission::READ_TASK_PERMISSION) || $userTenantGroup->can(Permission::READ_TASK_PERMISSION)) {
            return true;
        }
        return false;
    }

    public function unarchivedBoard(User $user, Board $board)
    {

        $group = $board->group;
        if (!$group) {
            abort(404, 'Group is not found');
        }
        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }
        if ($userTenant->can(Permission::UPDATE_BOARD_PERMISSION) || $userTenantGroup->can(Permission::UPDATE_BOARD_PERMISSION)) {
            return true;
        }
        return false;
    }

    public function changeBoardOfTask(User $user, Board $board, Task $task)
    {
        if ($board->id === $task->board_id) {
            abort(403, 'The task already belongs to this board');
        }
        $group = $board->group;
        if (!$group) {
            abort(404, 'Group is not found');
        }
        if (!$group->boards()->where('id', $task->board_id)->first()) {
            abort(403, 'The board doesn\'t belong to this group');
        }

        $userTenant = $user->getChosenTenant();
        $userTenantGroup = Auth::userTenantGroup($group->id);
        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }
        if ($userTenant->can(Permission::UPDATE_BOARD_PERMISSION) || $userTenantGroup->can(Permission::UPDATE_BOARD_PERMISSION)) {
            return true;
        }
        return false;
    }
}
