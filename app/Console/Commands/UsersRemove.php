<?php

namespace App\Console\Commands;

use App\Models\Board;
use App\Models\Budget;
use App\Models\Group;
use App\Models\Log;
use App\Models\PersonalDeadline;
use App\Models\Priority;
use App\Models\Task;
use App\Models\TaskSortOrder;
use App\Models\Timer;
use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantSettings;
use App\Models\UserTenantTask;
use App\Models\UserTenantTemplates;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class UsersRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:remove {email? : The email of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user and all associated data';

    /**
     * @var int
     */
    private $defaultConfirm = 1;

    /**
     * @var Collection|array
     */
    private $members = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        if ($user = $this->getUser()) {
            $this->warn('********************************************************************');
            $this->warn('***                                                              ***');
            $this->warn('***      Be careful, and backup your data before deleting!       ***');
            $this->warn('***                                                              ***');
            $this->warn('***      Also note that the functional is being finalized,       ***');
            $this->warn('***   these command are relevant for the version of 02/28/2019   ***');
            $this->warn('***                                                              ***');
            $this->warn('********************************************************************');

            if ($this->confirm('Are you sure you want to delete this user and all data related to him?', $this->defaultConfirm)) {
                DB::transaction(function() use ($user) {
                    if ($user->isOwner()) {
                        $this->removeTenantData($user);
                    }

                    $this->removeUserData($user);
                });

                \Artisan::call('remove:draft-tasks');
                \Artisan::call('remove:unused-tenants');
                \Artisan::call('remove:unused-groups');
                \Artisan::call('remove:unused-priorities');
                \Artisan::call('remove:unused-budgets');
                \Artisan::call('remove:logs-without-timers');
                \Artisan::call('remove:unused-attachments');

                \Artisan::call('computing:timers');
                \Artisan::call('computing:timer_billings');
            }

            $this->info('Finish');
        }
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        $email = $this->argument('email') ?? $this->ask('Which email?');
        $users = User::whereRaw("UPPER(`email`) LIKE '%". strtoupper($email)."%'")->get();

        if ($users->isEmpty()) {
            $this->warn('User with such email not found!');
            return null;
        }

        if ($users->count() > 1) {
            $selectEmails = $users->pluck('email')->toArray();
            $selectUser   = $this->choice('Which user?', $selectEmails);

            $user = $users->where('email', $selectUser)->first();
        } else {
            $user = $users->first();
        }

        $this->showUsersInfo([$this->getUserInfo($user)]);

        if ($user->isOwner()) {
            $users = $this->getMembers($user->user_tenant)->map(function($member) {
                return $this->getUserInfo($member);
            });

            $this->warn('This user is a tenant');

            if ($users->count()) {
                $this->warn('When deleting it, the following users will also be deleted, as well as their data.');
                $this->showUsersInfo($users->toArray());
            }

            if ($this->confirm('Are you sure about this?', $this->defaultConfirm)) {
                return $user;
            }

            return null;
        }

        return $user;
    }

    /**
     * @param User $user
     * @param bool $removeOwner
     *
     * @throws \Exception
     */
    public function removeUserData(User $user, bool $removeOwner = false)
    {
        $this->info("Deleting user data {$user->email}");

        Activity::where('causer_type', User::class)
            ->where('causer_id', $user->id)
            ->delete();

        TaskSortOrder::where('user_id', $user->id)
            ->delete();

        $user->userProfile()->delete();
        $user->attachments()->delete();
        $user->billTimer()->forceDelete();
        $user->comments()->delete();
        $user->images()->delete();
        $user->notifications()->delete();
        $user->notificationSubscription()->delete();
        $user->notificationTypeUser()->delete();
        $user->pushSubscriptions()->delete();

        if ($user->user_tenant) {
            $user->user_tenant->filters()->delete();
            $user->user_tenant->userTenantGroups()->delete();
            $user->user_tenant->userTenantRoles()->delete();

            PersonalDeadline::where('user_tenant_id', $user->user_tenant->id)
                ->delete();
            UserTenantSettings::where('user_tenant_id', $user->user_tenant->id)
                ->delete();
            UserTenantTemplates::where('user_tenant_id', $user->user_tenant->id)
                ->delete();
            UserTenantTask::where('user_tenant_id', $user->user_tenant->id)
                ->delete();
            Log::whereHas('timer', function ($query) use ($user) {
                $query->where('user_tenant_id', $user->user_tenant->id);
            })->delete();

            Timer::where('user_tenant_id', $user->user_tenant->id)
                ->delete();

            $doneById = null;

            if (!$removeOwner) {
                $owner = UserTenant::withoutGlobalScopes()
                    ->where('tenant_id', $user->user_tenant->tenant_id)
                    ->where('is_owner', 1)
                    ->first();

                if ($owner) {
                    $doneById = $owner->tenant_id !== $user->user_tenant->id ? $owner->id : null;
                }
            }

            Task::where('done_by', $user->user_tenant->id)
                ->update(['done_by' => $doneById]);

            $user->user_tenant->delete();
        }

        $user->delete();
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function removeTenantData(User $user)
    {
        /** @var UserTenant $userTenant */
        $userTenant = $user->user_tenant;

        foreach ($this->getMembers($userTenant) as $member) {
            $this->removeUserData($member, true);
        }

        $groups = app('GroupSer')->getGroupsWithRelationsByUserTenantId($userTenant->id);
        $groupIds = $groups->pluck('id')->toArray();

        $boards = $groups->pluck('boards')->collapse();
        $boardIds = $boards->pluck('id')->toArray();

        $tasks = $boards->pluck('tasks')->collapse();
        $taskIds = $tasks->pluck('id')->toArray();

        $boardBudgetIds = $boards->pluck('budget_id');
        $taskBudgetIds = $tasks->pluck('budget_id');
        $budgetIds = $boardBudgetIds->concat($taskBudgetIds);

        $priorities = app('PrioritySer')->getPrioritiesByUserTenantId($userTenant->id);
        $priorityIds = $priorities->whereIn('board_id', $boardIds)->pluck('id')->toArray();

        Group::whereIn('id', $groupIds)->delete();
        Board::whereIn('id', $boardIds)->delete();
        Task::whereIn('id', $taskIds)->delete();

        Activity::where('subject_type', Task::class)
            ->whereIn('subject_id', $taskIds)
            ->delete();

        Budget::whereIn('id', $budgetIds)->delete();
        Priority::whereIn('id', $priorityIds)->delete();

        $userTenant->tenant->customers()->delete();
        $userTenant->tenant->subscription()->delete();
        $userTenant->tenant->pipelines()->delete();
        $userTenant->tenant->customRoles()->delete();
        $userTenant->tenant->customPriorities()->delete();
        $userTenant->tenant->delete();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserInfo(User $user)
    {
        return [
            'id'            => $user->id,
            'full_name'     => $user->name.' '.$user->last_name,
            'email'         => $user->email,
            'status'        => $user->is_confirmed ? 'confirmed' : 'unconfirmed',
            'last_activity' => $user->last_activity,
        ];
    }

    /**
     * @param array $users
     */
    public function showUsersInfo(array $users)
    {
        $this->table(['Id', 'Full Name', 'Email', 'Status', 'Last Activity'], $users);
    }

    /**
     * @param UserTenant $userTenant
     *
     * @return array|Collection
     */
    public function getMembers(UserTenant $userTenant)
    {
        if (empty($this->members)) {
            $members = app('TenantSer')->getUserTenantWithRelationsByTenantId($userTenant->tenant_id);
            $userIds = $members->pluck('user')->pluck('id')->toArray();

            $this->members = User::whereIn('id', $userIds)
                ->where('id', '!=', $userTenant->user_id)
                ->get();
        }

        return $this->members;
    }
}
