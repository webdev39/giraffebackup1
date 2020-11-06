<?php

namespace App\Services\Group;

use App\Collections\BoardCollection;
use App\Collections\GroupCollection;
use App\Collections\TaskCollection;
use App\Collections\TimerCollection;
use App\Models\Board;
use App\Models\Group;
use App\Models\Role;
use App\Models\Task;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use App\Repositories\GroupRepositoryEloquent;
use App\Repositories\UserTenantGroupRepositoryEloquent;
use App\Services\BaseService;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class GroupService
 *
 * @package App\Services\Group
 */
class GroupService extends BaseService
{
    /** @var GroupRepositoryEloquent */
    public $groupRepo;

    /** @var UserTenantGroupRepositoryEloquent */
    private $userTenantGroupRepo;

    /**
     * GroupService constructor.
     */
    public function __construct()
    {
        $this->groupRepo            = app('GroupRepo');
        $this->userTenantGroupRepo  = app('UserTenantGroupRepo');
    }

    /**
     * @param int $groupId
     *
     * @return mixed
     */
    public function getGroupModelById(int $groupId)
    {
        return $this->groupRepo->findOrFail($groupId);
    }

    /**
     * @param int $userTenantId
     * @param int $groupId
     *
     * @return mixed
     */
    public function getUserTenantGroup(int $userTenantId, int $groupId)
    {
        return $this->userTenantGroupRepo->findWhere([
            'user_tenant_id'    => $userTenantId,
            'group_id'          => $groupId,
        ])->first();
    }

    /**
     * @param Collection $groups
     * @param int|null   $userTenantId
     *
     * @return GroupCollection
     */
    public function addGroupRelations(Collection $groups, int $userTenantId = null) : GroupCollection
    {
        $groupIds    = $groups->pluck('id')->unique()->toArray();
        $boards      = app('BoardRepo')->getBoardsByGroupIds($groupIds);
        $members     = app('UserTenantRepo')->getUserTenantsByGroupIds($groupIds);
        $permissions = app('UserTenantGroupRepo')->getUserTenantGroupPermissionsByGroupIds($groupIds, $userTenantId);

        $boards = BoardCollection::make($boards);

        $boardsId = $boards->pluck('id')->unique()->toArray();
        $countTask = Board::select('id', 'name')
            ->whereIn('id', $boardsId)
            ->with(['activeTasks' => function($query) {
                $query->where('tasks.draft', 0)
                    ->orWhere('tasks.draft', Auth::userTenantId());
            }])
            ->get();

        $boards->map(function($item) use ($countTask) {
            $item->{'tasks'} = TaskCollection::make($countTask->where('id', $item->id)->first()->activeTasks);
            $item->{'tasks_count'} = $countTask->where('id', $item->id)->first()->activeTasks->count();
        });

        $groups = GroupCollection::make($groups);

        $groups->setPermissions($permissions);
        $groups->setAttributes([
            'boards'    => $boards->groupBy('group_id'),
            'members'   => $members->groupBy('group_id'),
        ]);

        return $groups;
    }

    /**
     * @param Collection $groups
     * @param int|null   $userTenantId
     *
     * @return GroupCollection
     */
    public function addGroupRelationsWithTask(Collection $groups, int $userTenantId = null) : GroupCollection
    {
        $groupIds    = $groups->pluck('id')->unique()->toArray();

        $boards      = app('BoardRepo')->getBoardsByGroupIds($groupIds);
        $boardIds    = $boards->pluck('id')->unique()->toArray();

        $tasks       = app('TaskRepo')->getActiveTasksByBoardIds($boardIds, $userTenantId);
        $taskIds     = $tasks->pluck('id')->unique()->toArray();

        $timers      = app('TimerRepo')->getTimersByTaskIds($taskIds);
        $timerIds    = $timers->pluck('id')->unique()->toArray();

        $sortOrder   = app('TaskSortOrderRepo')->getSortOrderByTaskIds($taskIds);
        $members     = app('UserTenantRepo')->getUserTenantsByGroupIds($groupIds);
        $pauses      = app('PauseRepo')->getPausesByTimerIds($timerIds);
        $permissions = app('UserTenantGroupRepo')->getUserTenantGroupPermissionsByGroupIds($groupIds, $userTenantId);

        $countComments      = app('CommentRepo')->getCountCommentsByTaskIds($taskIds);
        $countAttachments   = app('CommentRepo')->getCountAttachmentsByTaskIds($taskIds);
        $countDoneSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, true);
        $countOpenSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, false);

        $taskSubscribers    = app('UserTenantRepo')->getTaskSubscribersByTaskIds($taskIds);
        $notifySubscribers  = app('UserTenantRepo')->getNotifySubscribersByTaskIds($taskIds);

        $timers = TimerCollection::make($timers);
        $timers->setAttributes([
            'pauses' => $pauses->groupBy('timer_id'),
        ]);

        $tasks = TaskCollection::make($tasks);
        $tasks->setSortOrder($sortOrder->groupBy('task_id'));
        $tasks->setAttributes([
            'timers'                => $timers->groupBy('task_id'),
            'task_subscribers'      => $taskSubscribers->groupBy('task_id'),
            'notify_subscribers'    => $notifySubscribers->groupBy('task_id'),
        ]);

        $tasks->setCountAttributes([
            'comment'               => $countComments,
            'attachment'            => $countAttachments,
            'done_sub_task'         => $countDoneSubTasks,
            'open_sub_task'         => $countOpenSubTasks,
        ]);

        $boards = BoardCollection::make($boards);
        $boards->setAttributes([
            'tasks' => $tasks->groupBy('board_id'),
        ]);

        $groups = GroupCollection::make($groups);
        $groups->setPermissions($permissions);
        $groups->setAttributes([
            'boards'        => $boards->groupBy('group_id'),
            'members'       => $members->groupBy('group_id'),
        ]);

        return $groups;
    }

    /**
     * @param int $groupId
     *
     * @return mixed
     */
    public function getGroupById(int $groupId)
    {
        return $this->groupRepo->getGroupsByIds([$groupId])->first();
    }

    /**
     * @param int      $groupId
     * @param int|null $userTenantId
     *
     * @return mixed
     */
    public function getGroupWithRelationsById(int $groupId, int $userTenantId = null)
    {
        $groups = $this->groupRepo->getGroupsByIds([$groupId]);

        return $this->addGroupRelations($groups, $userTenantId)->first();
    }

    /**
     * @param array $groupIds
     *
     * @return GroupCollection
     */
    public function getGroupsByIds(array $groupIds) : GroupCollection
    {
        return $this->groupRepo->getGroupsByIds($groupIds);
    }

    /**
     * @param array    $groupIds
     * @param int|null $userTenantId
     *
     * @return GroupCollection
     */
    public function getGroupsWithRelationsByIds(array $groupIds, int $userTenantId = null) : GroupCollection
    {
        $groups = $this->getGroupsByIds($groupIds);

        return $this->addGroupRelations($groups, $userTenantId);
    }

    /**
     * @param int       $userTenantId
     * @param bool|null $isArchived
     *
     * @return Collection
     */
    public function getGroupsByUserTenantId(int $userTenantId, bool $isArchived = null) : Collection
    {
        return $this->groupRepo->getGroupsByUserTenantId($userTenantId, $isArchived);
    }

    /**
     * @param int       $tenantId
     * @param bool|null $isArchived
     *
     * @return Collection
     */
    public function getGroupsByTenantId(int $tenantId, bool $isArchived = null) : Collection
    {
        return $this->groupRepo->getGroupsByTenantId($tenantId, $isArchived);
    }

    /**
     * @param int       $userTenantId
     * @param bool|null $isArchived
     *
     * @return GroupCollection
     */
    public function getGroupsWithRelationsByUserTenantId(int $userTenantId, bool $isArchived = null) : GroupCollection
    {
        $groups = $this->groupRepo->getGroupsByUserTenantId($userTenantId, $isArchived);

        return $this->addGroupRelations($groups, $userTenantId);
    }

    /**
     * @param int $tenantId
     * @param bool|null $isArchived
     * @return GroupCollection
     */
    public function getGroupsWithRelationsByTenantId(int $tenantId, bool $isArchived = null) : GroupCollection
    {
        $groups = $this->groupRepo->getGroupsByTenantId($tenantId, $isArchived);

        return $this->addGroupRelationsWithTask($groups);
    }

    /**
     * @param string $roleName
     * @param int    $userTenantId
     *
     * @return mixed
     */
    public function getManualCustomRole(string $roleName, int $userTenantId)
    {
        return app('UserTenantRepo')->getManualCustomRole($roleName, $userTenantId);
    }

    /**
     * @param int $groupId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function cloneGroup(int $groupId)
    {
        $group = $this->groupRepo->find($groupId);

        return DB::transaction(function () use ($group) {
            /** @var Group $cloneGroup */
            $cloneGroup         = $group->replicate();
            $cloneGroup->name   = $group->name.' ('.Carbon::now(auth()->user()->userProfile->time_zone)->toDateTimeString().')';
            $cloneGroup->push();

            $group->load('userTenantGroups.roles');

            /** @var UserTenantGroup $userTenantGroup */
            foreach ($group->userTenantGroups as $userTenantGroup) {
                /** @var UserTenantGroup $cloneUserTenantGroup */
                $cloneUserTenantGroup           = $userTenantGroup->replicate();
                $cloneUserTenantGroup->group_id = $cloneGroup->id;
                $cloneUserTenantGroup->push();

                foreach ($userTenantGroup->roles as $role) {
                    $cloneUserTenantGroup->attachRole($role);
                }
            }

            return $cloneGroup;
        });
    }

    /**
     * @param string $groupName
     * @param int    $userTenantId
     *
     * @return bool
     */
    public function hasUserGroupWithName(string $groupName, int $userTenantId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = app('UserTenantRepo')->find($userTenantId);

        return (bool) $userTenant->groups->where('name', '===', $groupName)->count();
    }

    /**
     * @param array      $attributes
     * @param UserTenant $userTenant
     * @param int|null   $groupId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function createOrUpdateGroup(array $attributes, UserTenant $userTenant, int $groupId = null)
    {
        $attributes['creator_id'] = $userTenant->user_id;
        $attributes['tenant_id'] = $userTenant->tenant_id;

        return DB::transaction(function () use ($attributes, $userTenant, $groupId) {
            $group = $this->groupRepo->updateOrCreate(['id' => $groupId], $attributes);

            if(!$groupId) {
                $this->createUserTenantGroupLink($userTenant->id, $group->id, true);
            }

            if(isset($attributes['members']) && $attributes['members']) {
                foreach ($attributes['members'] as $member) {
                    $userTenantGroup = $this->getUserTenantGroup($member['user_tenant_id'], $group->id);

                    if (!$userTenantGroup) {
                        $userTenantGroup = $this->createUserTenantGroupLink($member['user_tenant_id'], $group->id);

                        if (!$role = app('RoleSer')->getCustomRoleById($member['role_id'])) {
                            throw new \Exception('User Tenant has no the role', 404);
                        }

                        if (in_array($role->name, Role::TENANT_LEVEL_ROLES_NAMES)) {
                            throw new \Exception('You cant attach this role', 422);
                        }

                        $this->attachRoleToGroupMember($userTenantGroup, $role);
                    }
                }
            }

            return $group;
        });
    }

    /**
     * @param int $groupId
     *
     * @return int
     */
    public function destroyGroup(int $groupId)
    {
        return $this->groupRepo->delete($groupId);
    }

    /**
     * @param int  $userTenantId
     * @param int  $groupId
     * @param bool $isCreator
     *
     * @return UserTenantGroup
     */
    public function createUserTenantGroupLink(int $userTenantId, int $groupId, bool $isCreator = false)
    {
        $roleName = $this->getManualCustomRole($isCreator ? Role::GROUP_LEADER_ROLE['name'] : 'member', $userTenantId);

        /** @var UserTenantGroup $userTenantGroup */
        $userTenantGroup = $this->userTenantGroupRepo->firstOrCreate([
            'group_id'          => $groupId,
            'is_creator'        => $isCreator,
            'user_tenant_id'    => $userTenantId,
        ]);

        $userTenantGroup->attachRoleByName($roleName, $userTenantId);

        return $userTenantGroup;
    }

    /**
     * @param int $userTenantGroupId
     *
     * @return int
     */
    public function removeUserTenantGroupLink(int $userTenantGroupId)
    {
        return $this->userTenantGroupRepo->delete($userTenantGroupId);
    }

    /**
     * @param array $userTenantIds
     * @param int   $groupId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function attachUserTenantToGroup(array $userTenantIds, int $groupId)
    {
        return DB::transaction(function () use ($userTenantIds, $groupId) {
            foreach ($userTenantIds as $userTenantId) {
                $userTenantGroup = $this->getUserTenantGroup($userTenantId, $groupId);

                if (!$userTenantGroup) {
                    $this->createUserTenantGroupLink($userTenantId, $groupId);
                }
            }
        });
    }

    /**
     * @param array $userTenantIds
     * @param int   $groupId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function detachUserTenantFromGroup(array $userTenantIds, int $groupId)
    {
        return DB::transaction(function () use ($userTenantIds, $groupId) {
            foreach ($userTenantIds as $userTenantId) {
                $userTenantGroup = $this->getUserTenantGroup($userTenantId, $groupId);

                if ($userTenantGroup) {
                    $this->removeUserTenantGroupLink($userTenantGroup->id);
                }
            }
        });
    }

    /**
     * @param UserTenantGroup $userTenantGroup
     * @param int             $userTenantId
     */
    public function attachDefaultRoleToUserTenantGroup(UserTenantGroup $userTenantGroup, int $userTenantId)
    {
        $role = app('RoleRepo')->getRoleIdByName(Role::GROUP_MEMBER_ROLE['name'], $userTenantId);

        $userTenantGroup->attachRole($role);
    }

    /**
     * @param UserTenantGroup $userTenantGroup
     * @param Role            $role
     */
    public function attachRoleToGroupMember(UserTenantGroup $userTenantGroup, Role $role)
    {
        $userTenantGroup->detachRoles($userTenantGroup->roles);

        if (!$userTenantGroup->hasRole($role->name)) {
            $userTenantGroup->attachRole($role);
        }
    }

    /**
     * @param UserTenantGroup $userTenantGroup
     * @param Role            $role
     */
    public function detachRoleFromGroupMember(UserTenantGroup $userTenantGroup, Role $role)
    {
        if ($userTenantGroup->hasRole($role->name)) {
            $userTenantGroup->detachRole($role);
        }
    }

    /**
     * @param array      $groupRoles
     * @param UserTenant $userTenant
     *
     * @throws \Throwable
     */
    public function attachUserToGroupWithRoles(array $groupRoles, UserTenant $userTenant)
    {
        DB::transaction(function () use ($groupRoles, $userTenant)
        {
            foreach ($groupRoles as $groupRole) {
                $group  = $this->getGroupModelById($groupRole['group_id']);
                $role   = app('RoleSer')->getCustomRoleById($groupRole['role_id']);

                if (!$role) {
                    abort(404, 'Role is not found');
                }

                $userTenantGroup = $this->createUserTenantGroupLink($userTenant->id, $group->id);

                $this->attachRoleToGroupMember($userTenantGroup, $role);
            }
        });
    }

    /**
     * @param UserTenant $userTenant
     *
     * @return bool
     */
    public function detachUserTenantAllGroups(UserTenant $userTenant)
    {
        foreach ($userTenant->userTenantGroups as $userTenantGroups) {
            $userTenantGroups->detachRoles();
        }

        $userTenant->groups()->detach();

        return true;
    }

    /**
     * @param int  $groupId
     * @param bool $archived
     *
     * @return bool
     * @throws \Throwable
     */
    public function changeArchivedGroup(int $groupId, bool $archived = false)
    {
        $group      = $this->getGroupWithRelationsById($groupId);
        $boardIds   = $group->boards->pluck('id')->unique()->toArray();

        /** @var Collection $tasks */
        $tasks      = $group->boards->pluck('tasks');
        $taskIds    = $tasks->pluck('id')->unique()->toArray();
        $timerIds   = $tasks->pluck('timers')->collapse()->pluck('id')->unique()->toArray();

        DB::transaction(function () use ($boardIds, $taskIds, $timerIds, $groupId, $archived) {
            app('BoardSer')->changeIsArchiveBoardByIds($boardIds, $archived);
            app('TaskSer')->changeIsArchiveTaskByIds($taskIds, $archived);

            app('TimerSer')->stopTimers($timerIds);

            $this->changeIsArchiveGroupByIds([$groupId], $archived);
        });

        return true;
    }

    /**
     * @param array $groupIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveGroupByIds(array $groupIds, bool $isArchive = false)
    {
        $this->groupRepo->changeIsArchiveGroupByIds($groupIds, $isArchive);
    }

    /**
     * @param int $groupId
     *
     * @return bool
     */
    public function checkTrackedTimeInGroup(int $groupId) : bool
    {
        return app('TimerRepo')->getCountTimersInGroup($groupId) > 0;
    }

    /**
     * @param int $groupId1
     * @param int $groupId2
     *
     * @return Collection
     */
    public function getDiffMembersBetweenGroups(int $groupId1, int $groupId2) : Collection
    {
        $members1 = app('UserTenantRepo')->getUserTenantsByGroupIds([$groupId1])->unique('id');
        $members2 = app('UserTenantRepo')->getUserTenantsByGroupIds([$groupId2])->unique('id');

        return $members1->filter(function ($value) use ($members2) {
            return !$members2->firstWhere('id', $value->id);
        });
    }
}
