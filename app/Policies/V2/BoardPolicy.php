<?php

namespace App\Policies\V2;

use App\Models\Board;
use App\Models\Group;
use App\Models\User;

class BoardPolicy extends \App\Policies\BoardPolicy
{   /**
     * @param User $user
     * @param Board $board
     * @return bool
     */
    public function getAccess(User $user, Board $board)
    {
        return $board->isCreator($user) ||
            (
                $user->can('getAccess', $board->group) &&
                ($user->able('READ_BOARD') || optional($board->getUserTenantGroup($user))->able('READ_BOARD'))
            );
    }

    /**
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function create(User $user, Group $group)
    {
        return $user->can('getAccess', $group) &&
            ($user->able('CREATE_BOARD') || optional($group->getUserTenantGroup($user))->able('CREATE_BOARD'));
    }

    /**
     * @param User $user
     * @param Board $board
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(User $user, Board $board)
    {
        return $board->isCreator($user) ||
            (
                $user->can('getAccess', $board->group) &&
                ($user->able('UPDATE_BOARD') || optional($board->getUserTenantGroup($user))->able('UPDATE_BOARD'))
            );
    }

    /**
     * @param User $user
     * @param Board $board
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function destroy(User $user, Board $board)
    {
        return $board->isCreator($user) ||
            (
                $user->can('getAccess', $board->group) &&
                ($user->able('DELETE_BOARD') || optional($board->getUserTenantGroup($user))->able('DELETE_BOARD'))
            );
    }
}
