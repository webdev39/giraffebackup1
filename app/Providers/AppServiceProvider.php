<?php

namespace App\Providers;

use App\Services\Action\ActionService;
use App\Services\ActivityLog\ActivityLogService;
use App\Services\AuthService;
use App\Services\Bill\BillService;
use App\Services\Bill\Pdf\BillPdfService;
use App\Services\Billing\BillingService;
use App\Services\Board\BoardService;
use App\Services\Budget\BudgetService;
use App\Services\Comment\CommentService;
use App\Services\Customer\CustomerService;
use App\Services\Filter\FilterService;
use App\Services\Group\GroupService;
use App\Services\Image\ImageService;
use App\Services\Log\TimerLogService;
use App\Services\Notification\NotificationService;
use App\Services\Permission\PermissionService;
use App\Services\AbilityService;
use App\Services\Priority\PriorityService;
use App\Services\Reaction\ReactionService;
use App\Services\Reports\ReportsService;
use App\Services\Role\RoleService;
use App\Services\Pipeline\PipelineService;
use App\Services\Task\TaskService;
use App\Services\Tenant\TenantService;
use App\Services\Time\TimeService;
use App\Services\Timer\TimerService;
use App\Services\Repeat\RepeatService;
use App\Services\User\UserService;
use App\Services\UserProfile\UserProfileService;
use App\Services\UserTask\UserTaskService;
use App\Services\TaskSortOrder\TaskSortOrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Relation::morphMap([
            'comment' => 'App\Models\Comment',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton('AuthSer', function ($app) {
            return new AuthService(new JWTAuth(
                $app['tymon.jwt.manager'],
                $app['tymon.jwt.provider.user'],
                $app['tymon.jwt.provider.auth'],
                $app['request']
            ));
        });

        $this->app->singleton('UserProfileSer', function () {
            return new UserProfileService();
        });

        $this->app->singleton('TenantSer', function () {
            return new TenantService();
        });

        $this->app->singleton('TimerSer', function () {
            return new TimerService();
        });

        $this->app->singleton('RoleSer', function () {
            return new RoleService();
        });

        $this->app->singleton('PipelineSer', function () {
            return new PipelineService();
        });

        $this->app->singleton('PermissionSer', function () {
            return new PermissionService();
        });

        $this->app->singleton('GroupSer', function () {
            return new GroupService();
        });

        $this->app->singleton('BoardSer', function () {
            return new BoardService();
        });

        $this->app->singleton('TaskSer', function () {
            return new TaskService();
        });

        $this->app->singleton('BudgetSer', function () {
            return new BudgetService();
        });

        $this->app->singleton('CommentSer', function () {
            return new CommentService();
        });

        $this->app->singleton('UserTaskSer', function () {
            return new UserTaskService();
        });

        $this->app->singleton('UserSer', function () {
            return new UserService();
        });

        $this->app->singleton('FilterSer', function () {
            return new FilterService();
        });

        $this->app->singleton('PrioritySer', function () {
            return new PriorityService();
        });

        $this->app->singleton('TimerLogSer', function () {
            return new TimerLogService();
        });

        $this->app->singleton('CustomerSer', function () {
            return new CustomerService();
        });

        $this->app->singleton('BillingSer', function () {
            return new BillingService();
        });

        $this->app->singleton('ReportsSer', function () {
            return new ReportsService();
        });

        $this->app->singleton('BillSer', function () {
            return new BillService();
        });

        $this->app->singleton('BillPdfSer', function () {
            return new BillPdfService();
        });

        $this->app->singleton('NotificationSer', function () {
            return new NotificationService();
        });

        $this->app->singleton('ImageSer', function () {
            return new ImageService();
        });

        $this->app->singleton('TimeSer', function () {
            return new TimeService();
        });

        $this->app->singleton('TaskSortOrderSer', function () {
            return new TaskSortOrderService();
        });

        $this->app->singleton('ActivityLogSer', function () {
            return new ActivityLogService();
        });

        $this->app->singleton('ActionSer', function () {
            return new ActionService();
        });

        $this->app->singleton('RepeatSer', function () {
            return new RepeatService();
        });


        $this->app->singleton(AbilityService::class, function () {
            return new AbilityService();
        });

        $this->app->singleton('ReactionSer', function () {
            return new ReactionService();
        });
    }
}
