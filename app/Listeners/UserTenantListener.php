<?php

namespace App\Listeners;

use App\Events\UserTenantHasBeenCreated;
use App\Models\Board;
use App\Models\Group;
use App\Models\Log;
use App\Models\Role;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\UserTenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserTenantListener
 *
 * @package App\Listeners
 */
class UserTenantListener
{
    /** @var array */
    const DEFAULT_TIMER = [
        '00:40:00', '00:15:00',
    ];

    /** @var array */
    const DEFAULT_ROLES = [
        Role::GROUP_LEADER_ROLE,
        Role::GROUP_MEMBER_ROLE,
        Role::EXTERNAL_ROLE,
    ];

    /**
     * @param UserTenantHasBeenCreated $event
     *
     * @return mixed
     * @throws \Throwable
     */
    public function createDefaultData(UserTenantHasBeenCreated $event)
    {
        $userTenant = $event->userTenant;
        $groupName  = $this->getGroupName($event->userTenant->tenant);

        Auth::setUser($userTenant->user);

        $roles     = $this->createDefaultRoles($userTenant->tenant_id);
        $group     = $this->createDefaultGroup($groupName, $userTenant);

        if ($event->createSeedData) {
            $this->createSeedData($userTenant, $roles, $group);
        }
    }

    /**
     * @param int $tenantId
     *
     * @return Collection
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function createDefaultRoles(int $tenantId) : Collection
    {
        $roles = collect();

        foreach (self::DEFAULT_ROLES as $defaultRole) {
            $oldPerms    = new Collection($defaultRole['permissions']);
            $newPerms    = $oldPerms->pluck('name')->all();

            $permissions = app('PermissionSer')->getPermissionsByNames($newPerms);
            $roles->push(app('RoleSer')->createCustomRole($defaultRole, $permissions, $tenantId, 1));
        }

        return $roles;
    }

    /**
     * @param string     $groupName
     * @param UserTenant $userTenant
     *
     * @return Group
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    private function createDefaultGroup(string $groupName, UserTenant $userTenant)
    {
        /** @var Group $group */
        $group = app('GroupSer')->createOrUpdateGroup(['name' => $groupName], $userTenant);

        /** @var Board $createdBoard */
        $board = app('BoardSer')->createBoard([
            'group_id'     => $group->id,
            'name'         => $groupName.' Board',
            'creator_id' => $userTenant->user_id
        ]);

        app('PrioritySer')->createUniqDefaultPriorities($board->id);

        $currentDay = \Carbon\Carbon::today();
        $tomorrow = \Carbon\Carbon::tomorrow();
        for ($i = 1; $i <= 2; $i++) {
            /** @var Task $task */
            $task = app('TaskSer')->create([
                'board_id'  => $board->id,
                'name'      => $groupName.' Task '.$i,
                'creator_id' => $userTenant->user_id,
                'planned_deadline'    => $currentDay,
                'deadline'    => $tomorrow,
                'soft_budget'    => '24:00'
            ], $userTenant->id);

            app('TaskSer')->attachUserTenantToTask($task, $userTenant->id);
            app('NotificationSer')->taskSubscribe($task, $userTenant->user->id);

            foreach (self::DEFAULT_TIMER as $time) {
                /** @var Log $log */
                app('TimerLogSer')->createOrUpdateTimerLog([
                    'task_id'        => $task->id,
                    'time'           => $time,
                    'user_tenant_id' => $userTenant->id,
                ]);
            }

            app('CommentSer')->createOrUpdateComment([
                'task_id'           => $task->id,
                'user_id'           => $userTenant->user_id,
                'body'              => 'Test comment',
            ]);
        }

        return $group;
    }

    /**
     * @param UserTenant $userTenant
     * @param Collection $groupRoles
     * @param Group      $group
     *
     * @throws \Throwable
     */
    public function createSeedData(UserTenant $userTenant, Collection $groupRoles, Group $group)
    {
        $faker = \Faker\Factory::create();

        $availableRoles = app('TenantSer')->getAvailableRoles();
        $userEmail = $userTenant->user->email;
        $domain = explode('@', $userEmail)[1];

        /** @var Role $availableRole */
        foreach ($availableRoles as $availableRole) {
            if ($availableRole->name == 'admin') {
                continue;
            }

            $attributes = [
                'name'          => $faker->firstName,
                'last_name'     => $faker->lastName,
                'password'      => 'secret',
                'email'         => $availableRole->name.'@'.$domain,
                'roles'   => $availableRole->name,
                'group_roles'   => [
                    ['group_id' => $group->id, 'role_id' => $groupRoles->first()->id],
                ],
            ];

            $userTenant = app('AuthSer')->inviteUser($attributes, $userTenant);

            app('AuthSer')->updateUserPassword('secret', $userTenant->id);
        }
    }

    /**
     * @param Tenant $tenant
     *
     * @return string
     */
    private function getGroupName(Tenant $tenant) : string
    {
        return $tenant->company_name ? $tenant->company_name : $tenant->project_name;
    }
}
