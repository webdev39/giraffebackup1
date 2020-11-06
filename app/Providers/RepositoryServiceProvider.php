<?php

namespace App\Providers;

use App\Repositories\FieldRepositoryEloquent;
use App\Repositories\AttachmentCommentRepositoryEloquent;
use App\Repositories\ReactionRepositoryEloquent;
use App\Repositories\PersonalDeadlineRepositoryEloquent;
use App\Repositories\ActionRepositoryEloquent;
use App\Repositories\ActivityLogRepositoryEloquent;
use App\Repositories\AttachmentRepositoryEloquent;
use App\Repositories\BillingStatusRepositoryEloquent;
use App\Repositories\BillRepositoryEloquent;
use App\Repositories\BillTimerRepositoryEloquent;
use App\Repositories\BoardPriorityRepositoryEloquent;
use App\Repositories\BoardRepositoryEloquent;
use App\Repositories\BoardTaskRepositoryEloquent;
use App\Repositories\BudgetRepositoryEloquent;
use App\Repositories\BudgetTypeRepositoryEloquent;
use App\Repositories\CommentAttachmentRepositoryEloquent;
use App\Repositories\CommentRepositoryEloquent;
use App\Repositories\CustomerRepositoryEloquent;
use App\Repositories\FilterRepositoryEloquent;
use App\Repositories\GroupRepositoryEloquent;
use App\Repositories\ImageRepositoryEloquent;
use App\Repositories\LogAttachmentRepositoryEloquent;
use App\Repositories\NotificationSubscriptionRepositoryEloquent;
use App\Repositories\PauseRepositoryEloquent;
use App\Repositories\PermissionRepositoryEloquent;
use App\Repositories\PipelineRepositoryEloquent;
use App\Repositories\PipelineRuleRepositoryEloquent;
use App\Repositories\PipelineFilterRepositoryEloquent;
use App\Repositories\PriorityRepositoryEloquent;
use App\Repositories\RepeatRepositoryEloquent;
use App\Repositories\RoleRepositoryEloquent;
use App\Repositories\SubscriptionRepositoryEloquent;
use App\Repositories\SubTaskRepositoryEloquent;
use App\Repositories\LogRepositoryEloquent;
use App\Repositories\TaskRepositoryEloquent;
use App\Repositories\TenantRepositoryEloquent;
use App\Repositories\TimerBillingRepositoryEloquent;
use App\Repositories\TimerLogRepositoryEloquent;
use App\Repositories\TimerRepositoryEloquent;
use App\Repositories\TenantCustomRoleRepositoryEloquent;
use App\Repositories\UserProfileRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserTaskRepositoryEloquent;
use App\Repositories\TaskSortOrderRepositoryEloquent;
use App\Repositories\UserTenantGroupRepositoryEloquent;
use App\Repositories\UserTenantPriorityRepositoryEloquent;
use App\Repositories\UserTenantRepositoryEloquent;
use App\Repositories\UserTenantRoleRepositoryEloquent;
use App\Repositories\UserTenantSettingsRepositoryEloquent;
use App\Repositories\UserTenantTaskRepositoryEloquent;
use App\Repositories\UserTenantTemplatesRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ImageRepo', function ($app) {
            return new ImageRepositoryEloquent($app);
        });
        $this->app->singleton('SubscriptionRepo', function ($app) {
            return new SubscriptionRepositoryEloquent($app);
        });
        $this->app->singleton('TenantRepo', function ($app) {
            return new TenantRepositoryEloquent($app);
        });
        $this->app->singleton('TimerRepo', function ($app) {
            return new TimerRepositoryEloquent($app);
        });
        $this->app->singleton('PauseRepo', function ($app) {
            return new PauseRepositoryEloquent($app);
        });
        $this->app->singleton('UserRepo', function ($app) {
            return new UserRepositoryEloquent($app);
        });
        $this->app->singleton('UserProfileRepo', function ($app) {
            return new UserProfileRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantRepo', function ($app) {
            return new UserTenantRepositoryEloquent($app);
        });
        $this->app->singleton('RoleRepo', function ($app) {
            return new RoleRepositoryEloquent($app);
        });
        $this->app->singleton('PipelineRepo', function ($app) {
            return new PipelineRepositoryEloquent($app);
        });
        $this->app->singleton('PipelineRuleRepo', function ($app) {
            return new PipelineRuleRepositoryEloquent($app);
        });
        $this->app->singleton('PipelineFilterRepo', function ($app) {
            return new PipelineFilterRepositoryEloquent($app);
        });
        $this->app->singleton('PermissionRepo', function ($app) {
            return new PermissionRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantRoleRepo', function ($app) {
            return new UserTenantRoleRepositoryEloquent($app);
        });
        $this->app->singleton('GroupRepo', function ($app) {
            return new GroupRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantGroupRepo', function ($app) {
            return new UserTenantGroupRepositoryEloquent($app);
        });
        $this->app->singleton('BoardRepo', function ($app) {
            return new BoardRepositoryEloquent($app);
        });
        $this->app->singleton('TaskRepo', function ($app) {
            return new TaskRepositoryEloquent($app);
        });
        $this->app->singleton('BudgetRepo', function ($app) {
            return new BudgetRepositoryEloquent($app);
        });
        $this->app->singleton('BudgetTypeRepo', function ($app) {
            return new BudgetTypeRepositoryEloquent($app);
        });
        $this->app->singleton('CustomerRepo', function ($app) {
            return new CustomerRepositoryEloquent($app);
        });
        $this->app->singleton('PriorityRepo', function ($app) {
            return new PriorityRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantTaskRepo', function ($app) {
            return new UserTenantTaskRepositoryEloquent($app);
        });
        $this->app->singleton('SubTaskRepo', function ($app) {
            return new SubTaskRepositoryEloquent($app);
        });
        $this->app->singleton('CommentRepo', function ($app) {
            return new CommentRepositoryEloquent($app);
        });
        $this->app->singleton('AttachmentRepo', function ($app) {
            return new AttachmentRepositoryEloquent($app);
        });
        $this->app->singleton('TenantCustomRoleRepo', function ($app) {
            return new TenantCustomRoleRepositoryEloquent($app);
        });
        $this->app->singleton('BoardTaskRepo', function ($app) {
            return new BoardTaskRepositoryEloquent($app);
        });
        $this->app->singleton('UserTaskRepo', function ($app) {
            return new UserTaskRepositoryEloquent($app);
        });
        $this->app->singleton('FilterRepo', function ($app) {
            return new FilterRepositoryEloquent($app);
        });
        $this->app->singleton('LogRepo', function ($app) {
            return new LogRepositoryEloquent($app);
        });
        $this->app->singleton('TimerLogRepo', function ($app) {
            return new TimerLogRepositoryEloquent($app);
        });
        $this->app->singleton('FilterRepo', function ($app) {
            return new FilterRepositoryEloquent($app);
        });
        $this->app->singleton('BoardPriorityRepo', function ($app) {
            return new BoardPriorityRepositoryEloquent($app);
        });
        $this->app->singleton('CommentAttachmentRepo', function ($app) {
            return new CommentAttachmentRepositoryEloquent($app);
        });
        $this->app->singleton('LogAttachmentRepo', function ($app) {
            return new LogAttachmentRepositoryEloquent($app);
        });
        $this->app->singleton('TimerBillingRepo', function ($app) {
            return new TimerBillingRepositoryEloquent($app);
        });
        $this->app->singleton('BillingStatusRepo', function ($app) {
            return new BillingStatusRepositoryEloquent($app);
        });
        $this->app->singleton('BillRepo', function ($app) {
            return new BillRepositoryEloquent($app);
        });
        $this->app->singleton('BillTimerRepo', function ($app) {
            return new BillTimerRepositoryEloquent($app);
        });
        $this->app->singleton('NotificationSubscriptionRepo', function ($app) {
            return new NotificationSubscriptionRepositoryEloquent($app);
        });
        $this->app->singleton('TaskSortOrderRepo', function ($app) {
            return new TaskSortOrderRepositoryEloquent($app);
        });
        $this->app->singleton('ActivityLogRepo', function ($app) {
            return new ActivityLogRepositoryEloquent($app);
        });
        $this->app->singleton('ActionRepo', function ($app) {
            return new ActionRepositoryEloquent(
                $app,
                $app->make('ActivityLogRepo'),
                $app->make('CommentRepo'),
                $app->make('TimerLogRepo'),
                $app->make('CommentSer')
            );
        });
        $this->app->singleton('PersonalDeadlineRepo', function ($app) {
            return new PersonalDeadlineRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantPriorityRepo', function ($app) {
            return new UserTenantPriorityRepositoryEloquent($app);
        });
        $this->app->singleton('FieldRepo', function ($app) {
            return new FieldRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantSettingsRepo', function ($app) {
            return new UserTenantSettingsRepositoryEloquent($app);
        });
        $this->app->singleton('UserTenantTemplatesRepo', function ($app) {
            return new UserTenantTemplatesRepositoryEloquent($app);
        });
        $this->app->singleton('AttachmentCommentRepo', function ($app) {
            return new AttachmentCommentRepositoryEloquent($app);
        });
        $this->app->singleton('RepeatRepo', function ($app) {
            return new RepeatRepositoryEloquent($app);
        });
        $this->app->singleton('ReactionRepo', function ($app) {
            return new ReactionRepositoryEloquent($app);
        });
    }
}
