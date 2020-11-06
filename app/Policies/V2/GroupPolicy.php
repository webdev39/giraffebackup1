<?php

namespace App\Policies\V2;

use App\Models\Group;
use App\Models\User;

class GroupPolicy extends \App\Policies\GroupPolicy
{

    /**
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->able('READ_GROUP');
    }

    public function show(User $user, Group $group)
    {
        return $group->isCreator($user) || (bool)$group->getUserTenantGroup($user)
            || $user->able('READ_ALL_GROUPS');
    }

    /**
     * @param User $user
     * @param Group $group
     * @return bool|mixed
     */
    public function getAccess(User $user, Group $group)
    {
        return $group->isCreator($user) || (bool)$group->getUserTenantGroup($user);
    }

    public function createComment(User $user, Group $group)
    {
        return $this->getAccess($user, $group);
    }

    public function cloneGroup(User $user, Group $group)
    {
        return $this->getAccess($user, $group) && $this->create($user);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function readAll(User $user)
    {
        return $user->able('READ_ALL_GROUPS');
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->able('CREATE_GROUP');
    }

    /**
     * @param User $user
     * @param Group $group
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(User $user, Group $group)
    {
        $hasReadJoinGroupRole = $user->user_tenant->roles->pluck('display_name')->contains('read+join-group');

        return $group->isCreator($user) || $hasReadJoinGroupRole ||
            (
                $user->can('getAccess', $group) &&
                ($user->able('UPDATE_GROUP') || $group->getUserTenantGroup($user)->able('UPDATE_GROUP'))
            );
    }

    /**
     * @param User $user
     * @param Group $group
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function destroy(User $user, Group $group)
    {
        $hasReadJoinGroupRole = $user->user_tenant->roles->pluck('display_name')->contains('read+join-group');
        return $group->isCreator($user) || $hasReadJoinGroupRole ||
            (
                $user->can('getAccess', $group) &&
                ($user->able('DELETE_GROUP') || $group->getUserTenantGroup($user)->able('DELETE_GROUP'))
            );
    }


    /**
     * @param User $user
     * @param Group $group
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function manage(User $user, Group $group)
    {
        $hasReadJoinGroupRole = $user->user_tenant->roles->pluck('display_name')->contains('read+join-group');

        return $hasReadJoinGroupRole || (
            $user->able('MANAGE_GROUP_MEMBERS') ||
            $group->getUserTenantGroup($user)->able('MANAGE_GROUP_MEMBERS')
        );
    }
}
