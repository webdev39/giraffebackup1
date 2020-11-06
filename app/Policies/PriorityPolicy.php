<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Permission;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PriorityPolicy
{
    use HandlesAuthorization;

    public function managePriority(User $user, Priority $priority, Board $board)
    {
        if (!app('BoardPriorityRepo')->findWhere(['board_id' => $board->id, 'priority_id' => $priority->id])->first()) {
            abort(403, 'You have no permissions to manage the priority');
        }

        $userTenant         = $user->getChosenTenant();
        $userTenantGroup    = Auth::userTenantGroup($board->group->id);

        if (!$userTenantGroup) {
            abort(403, 'User Tenant is not a member of this group');
        }

        Auth::failIfHasNoPermissionsOnTenantLevel(
            $userTenant,
            $userTenantGroup,
            [Permission::UPDATE_BOARD_PERMISSION]
        );

        return true;
    }
}
