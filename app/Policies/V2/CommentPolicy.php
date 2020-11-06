<?php


namespace App\Policies\V2;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentPolicy extends \App\Policies\CommentPolicy
{
    /**
     * @param User $user
     * @param Comment $comment
     * @param Task $task
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(User $user, Comment $comment, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);

        return $comment->isCreator($user) || (
            (
                $user->able('MANAGE_OTHER_TIME_LOGS') || optional($userTenantGroup)->able('MANAGE_OTHER_TIME_LOGS'))
                && $user->can('getAccess', $task)
            );
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @param Task $task
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function delete(User $user, Comment $comment, Task $task)
    {
        $userTenantGroup = $task->board->first()->getUserTenantGroup($user);

        return $comment->isCreator($user) || (
            (
                $user->able('MANAGE_OTHER_TIME_LOGS') || optional($userTenantGroup)->able('MANAGE_OTHER_TIME_LOGS'))
                && $user->can('getAccess', $task)
            );
    }

    public function manage(User $user, Comment $comment)
    {
        return $comment->isCreator($user) || $user->isOwner();
    }
}