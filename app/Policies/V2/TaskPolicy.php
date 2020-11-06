<?php

namespace App\Policies\V2;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;

class TaskPolicy extends \App\Policies\TaskPolicy
{
    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function getAccess(User $user, Task $task)
    {
        $board = $task->board->first();
        $userTenantGroup = $board->getUserTenantGroup($user);
        return $task->isCreator($user) ||
            (
                $user->can('getAccess', $board)
                && ($user->able('READ_TASK') || optional($userTenantGroup)->able('READ_TASK'))
            );
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function createComment(User $user, Task $task)
    {
        return $this->getAccess($user, $task);
    }

    /**
     * @param User $user
     * @param Board $board
     * @return bool
     */
    public function create(User $user, Board $board)
    {
        $userTenantGroup = $board->getUserTenantGroup($user);
        return $user->able('CREATE_TASK') || optional($userTenantGroup)->able('CREATE_TASK');
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function update(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);
        return $task->isCreator($user) ||
            ($user->able('UPDATE_TASK') || optional($userTenantGroup)->able('UPDATE_TASK'));
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function delete(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);
        return $task->isCreator($user) ||
            ($user->able('DELETE_TASK') || optional($userTenantGroup)->able('DELETE_TASK'));
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function assignMember(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);
        $ableToChangeAssignees = $task->isCreator($user) || $user->isOwner() || $this->update($user, $task);
        $hasAssignPermission = $user->able('ADD_ASSIGNEES_TASK') || optional($userTenantGroup)->able('ADD_ASSIGNEES_TASK');

        return $ableToChangeAssignees && $hasAssignPermission;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function unAssignMember(User $user, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);
        $ableToChangeAssignees = $task->isCreator($user) || $user->isOwner() || $this->update($user, $task);
        $hasAssignPermission = $user->able('DELETE_ASSIGNEES_TASK') || optional($userTenantGroup)->able('DELETE_ASSIGNEES_TASK');

        return $ableToChangeAssignees && $hasAssignPermission;
    }
}
