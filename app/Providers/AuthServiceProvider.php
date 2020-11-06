<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Filter;
use App\Models\Group;
use App\Models\Log;
use App\Models\Priority;
use App\Models\Role;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserColorScheme;
use App\Models\UserTenant;
use App\Policies\V2\BoardPolicy;
use App\Policies\V2\CommentPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FilterPolicy;
use App\Policies\V2\GroupPolicy;
use App\Policies\V2\LogPolicy;
use App\Policies\PriorityPolicy;
use App\Policies\RolePolicy;
use App\Policies\V2\TaskPolicy;
use App\Policies\TenantPolicy;
use App\Policies\V2\UserColorSchemePolicy;
use App\Policies\V2\UserPolicy;
use App\Policies\V2\UserTenantPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'       => 'App\Policies\ModelPolicy',
        Role::class       => RolePolicy::class,
        Tenant::class     => TenantPolicy::class,
        UserTenant::class => UserTenantPolicy::class,
        Group::class      => GroupPolicy::class,
        Board::class      => BoardPolicy::class,
        Task::class       => TaskPolicy::class,
        Comment::class    => CommentPolicy::class,
        Priority::class   => PriorityPolicy::class,
        Comment::class    => CommentPolicy::class,
        Filter::class     => FilterPolicy::class,
        Log::class        => LogPolicy::class,
        Customer::class   => CustomerPolicy::class,
        User::class       => UserPolicy::class,
        UserColorScheme::class => UserColorSchemePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
