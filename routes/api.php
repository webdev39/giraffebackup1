<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => '/v1', 'namespace' => 'Api'], function () {

    Route::group(['middleware' => 'guest', 'as' => 'auth.'], function () {
        Route::post('login',                    'AuthController@login');
        Route::post('register',                 'AuthController@register');

        Route::get('confirm',                   'AuthController@confirmHash')->name('getConfirm');
        Route::post('confirm',                  'AuthController@confirm')->name('confirm');

        Route::get('join',                      'AuthController@getJoin')->name('getJoin');
        Route::post('join',                     'AuthController@join')->name('join');

        Route::post('reset-password',           'AuthController@reset')->name('resetPassword');

        Route::get('restore-password',          'AuthController@getRestore')->name('getRestorePassword');
        Route::post('restore-password',         'AuthController@restore')->name('restorePassword');
    });

    Route::group(['middleware' => 'api.check'], function () {
        Route::get('authenticate',              'AuthController@authenticate')->name('authenticate');
        Route::get('logout',                    'AuthController@logout')->name('logout');

        /**
         * Tasks
         */
        Route::group(['prefix' => 'task', 'as' => 'task.'], function () {
            Route::get('/list',                 'TaskController@index')->name('index');

            Route::get('/board/{boardId}',      'TaskController@getTaskByBoardId')->name('board')->where('boardId', '[0-9]+');

            Route::get('/{taskId}',             'TaskController@show')->name('show')->where('taskId', '[0-9]+');
            Route::get('/last',                 'TaskController@getLatestActiveTasks')->name('last');
            Route::post('/create',              'TaskController@create')->name('create');

            Route::put('/{taskId}/read',        'TaskController@setRead')->name('setRead')->where('taskId', '[0-9]+');

            Route::put('/update',               'TaskController@update')->name('update');
            Route::put('/workflow/change',      'TaskController@changeWorkflow')->name('workflow.change');
            Route::put('/order/change/{type}',  'TaskController@changeOrder')->name('order.change')->where('type', implode('|', array_keys(\App\Models\TaskSortOrder::SORT_ORDER_TYPES)));

            Route::put('{taskId}/change-sort-weight', 'TaskController@changeSortWeight')->name('changeSortWeight')->where('taskId', '[0-9]+');

            Route::delete('/remove/{taskId}',   'TaskController@remove')->name('remove');

            Route::group(['prefix' => 'subscriber', 'as' => 'subscriber.'], function() {
                Route::post('attach',           'TaskController@assign')->name('attach');
                Route::post('detach',           'TaskController@unassign')->name('detach');
            });

            Route::post('subscribe-and-attach', 'TaskController@subscribeAndAssign')->name('subscribe-and-assign');

            Route::group(['prefix' => 'notification', 'as' => 'notification.'], function() {
                Route::post('subscribe',        'TaskController@subscribe')->name('subscribe');
                Route::post('unsubscribe',      'TaskController@unsubscribe')->name('unsubscribe');
            });

            Route::group(['prefix' => 'sub-task', 'as' => 'sub-task.'], function() {
                Route::get('/{taskId}',         'TaskController@subTaskList')->name('index')->where('taskId', '[0-9]+');
                Route::post('/create',          'TaskController@createSubTask')->name('create');
                Route::put('/update',           'TaskController@updateSubTask')->name('update');
                Route::put('/workflow/change',  'TaskController@changeSubTaskStatus')->name('workflow.change');
                Route::post('/order/change',    'TaskController@changeSubTaskOrder')->name('order.change');
                Route::delete('/{subTaskId}',   'TaskController@removeSubTask')->name('remove')->where('subTaskId', '[0-9]+');
            });
        });

        Route::group(['prefix' => 'user-task', 'as' => 'activity'], function() {
            Route::get('/',                     'UserTaskController@getUserTaskDeadline')->name('index');
            Route::get('{slug}',                'UserTaskController@getUserTaskActivity')->name('slug')->where('slug', implode('|', array_keys(\App\Services\UserTask\UserTaskService::AVAILABLE_SLUGS)));
            Route::get('/count',                'UserTaskController@getUserCountTaskActivity')->name('count')->where('slug', implode('|', array_keys(\App\Services\UserTask\UserTaskService::AVAILABLE_SLUGS)));
        });

        /**
         * Boards
         */
        Route::group(['prefix' => 'board', 'as' => 'board.'], function () {
            Route::get('/',                     'BoardController@index')->name('index');
            Route::get('/last',                 'BoardController@getLatestActiveBoards')->name('last');

            Route::get('/{boardId}',            'BoardController@show')->name('show')->where('boardId', '[0-9]+');
            Route::get('/{boardId}/archived',   'BoardController@archived')->name('archived')->where('boardId', '[0-9]+');
            Route::get('/{boardId}/unarchived', 'BoardController@unarchived')->name('unarchived')->where('boardId', '[0-9]+');

            Route::post('/create',              'BoardController@create')->name('create');
            Route::put('/update',               'BoardController@update')->name('update');
            Route::put('/update/group',         'BoardController@updateGroup')->name('update.group');
            Route::delete('/{boardId}',         'BoardController@destroy')->name('destroy')->where('boardId', '[0-9]+');
        });

        /**
         * Groups
         */
        Route::group(['prefix' => 'group', 'as' => 'group.'], function () {
            Route::get('/',                     'GroupController@index')->name('index');
            Route::get('all',                   'GroupController@all')->name('all');
            Route::get('/{groupId}',            'GroupController@show')->name('show')->where('groupId', '[0-9]+');
            Route::get('/{groupId}/clone',      'GroupController@cloneGroup')->name('clone')->where('groupId', '[0-9]+');

            Route::post('/create',              'GroupController@create')->name('create');
            Route::put('/update',               'GroupController@update')->name('update');
            Route::delete('/{groupId}',         'GroupController@destroy')->name('destroy')->where('groupId', '[0-9]+');

            Route::get('/{groupId}/archived',             'GroupController@archived')->name('archived')->where('groupId', '[0-9]+');
            Route::get('/{groupId}/unarchived',           'GroupController@unarchived')->name('unarchived')->where('groupId', '[0-9]+');

            Route::group(['prefix' => 'role', 'as' => 'role.'], function() {
                Route::post('/attach',           'GroupController@attachRoleToGroupUser')->name('attach');
                Route::post('/detach',           'GroupController@detachRoleToGroupUser')->name('detach');
            });

            Route::group(['prefix' => 'member', 'as' => 'member.'], function() {
                Route::post('/attach',           'GroupController@attachUserToGroup')->name('attach');
                Route::post('/detach',           'GroupController@detachUserToGroup')->name('detach');
            });
        });

        /**
         * Filters
         */
        Route::group(['prefix' => 'filter', 'as' => 'filter.'], function () {
            Route::get('/',                     'FilterController@index')->name('index');
            Route::get('{filterId}',            'FilterController@show')->name('show')->where('filterId', '[0-9]+');
            Route::post('/create',              'FilterController@create')->name('create');
            Route::put('/update',               'FilterController@update')->name('update');
            Route::delete('/{filterId}',        'FilterController@destroy')->name('destroy')->where('filterId', '[0-9]+');
        });

        /**
         * All actions for communication board
         */
        Route::group(['prefix' => 'action', 'as' => 'action.'], function () {
            Route::post('/task/{taskId}',       'ActionController@getActionByTask')->name('task')->where('taskId', '[0-9]+');
            Route::post('/board/{boardId}',     'ActionController@getActionByBoard')->name('board')->where('boardId', '[0-9]+');
            Route::post('/group/{groupId}',     'ActionController@getActionByGroup')->name('group')->where('groupId', '[0-9]+');
        });

        /**
         * Activity Logs
         */
        Route::group(['prefix' => 'activity_logs', 'as' => 'activity_logs.'], function() {
            Route::get('/',                     'ActivityLogController@getUserActivityLog')->name('user');
            Route::get('/task/{taskId}',        'ActivityLogController@getActivityLogByTask')->name('task');
            Route::get('/board/{boardId}',      'ActivityLogController@getActivityLogByBoard')->name('board');
            Route::get('/group/{groupId}',      'ActivityLogController@getActivityLogByGroup')->name('group');
        });

        /**
         * Timers
         */
        Route::group(['prefix' => 'timer', 'as' => 'timer.'], function() {
            Route::get('/',                     'TimerController@index')->name('index');
            Route::get('/{timerId}',            'TimerController@show')->name('show')->where('timerId', '[0-9]+');
            Route::get('/task/{taskId}',        'TimerController@showTaskTimer')->name('show.task')->where('taskId', '[0-9]+');
            Route::post('create',               'TimerController@create')->name('create');
            Route::post('create-start',         'TimerController@createStart')->name('create.start');
            Route::put('update',                'TimerController@update')->name('update');
            Route::post('start',                'TimerController@start')->name('start');
            Route::post('stop',                 'TimerController@stop')->name('stop');
            Route::post('pause',                'TimerController@pause')->name('pause');
            Route::post('continue',             'TimerController@continue')->name('continue');
            Route::delete('destroy',            'TimerController@destroy')->name('destroy');
        });

        /**
         * Logs
         */
        Route::group(['prefix' => 'log', 'as' => 'timer_log.'], function () {
            Route::get('/timer',                 'TimerLogController@index')->name('index');
            Route::get('/timer/{task}',          'TimerLogController@show')->name('show');
            Route::post('/timer',                'TimerLogController@create')->name('create');
            Route::put('/timer/{log}',           'TimerLogController@update')->name('update');
            Route::delete('/timer/{log}',        'TimerLogController@destroy')->name('delete');
        });

        /**
         * Comments
         */
        Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
            Route::get('/{commentId}',          'CommentController@show')->name('show')->where('commentId', '[0-9]+');
            Route::post('/create',              'CommentController@create')->name('create');
            Route::put('/update',               'CommentController@update')->name('update');
            Route::delete('/{clientId}',        'CommentController@destroy')->name('destroy')->where('clientId', '[0-9]+');
        });

        /**
         * Reactions
         */
        Route::group(['prefix' => 'reaction/{target}/{targetId}', 'as' => 'reaction.', 'where' => ['targetId' => '[0-9]+']], function () {
            Route::get('/likers',               'ReactionController@likers')->name('likers');
            Route::put('/like',                 'ReactionController@like')->name('like');

            Route::group(['prefix' => '{source}/{sourceId}', 'where' => ['sourceId' => '[0-9]+']], function () {
                Route::put('/stick',            'ReactionController@stick')->name('stick');
            });
        });

        /**
         * Attachments
         */
        Route::group(['prefix' => 'attachment', 'as' => 'comment.'], function () {
            Route::post('/save',                'AttachmentController@upload')->name('save');

            Route::group(['prefix' => '{attachmentId}/comment', 'as' => 'comment.', 'where' => ['attachmentId' => '[0-9]+']], function () {
                Route::get('/',                 'AttachmentController@showComments')->name('show');
                Route::post('/',                'AttachmentController@createComment')->name('create');
                Route::put('/',                 'AttachmentController@updateComment')->name('update');
                Route::delete('/{commentId}',   'AttachmentController@destroyComment')->name('destroy')->where('commentId', '[0-9]+');
            });
        });

        /**
         * Notifications
         */
        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function() {
            Route::get('/',                 'NotificationController@index')->name('index');
            Route::post('add-device',       'NotificationController@addDevice')->name('add_device');
            Route::get('/{id}/{status}',    'NotificationController@updateStatus')->name('status.update')->where('status', 'read|unread');
            Route::get('/all_mark_read',    'NotificationController@allMarkRead')->name('all_mark_read');
            Route::get('/unread',           'NotificationController@unread')->name('unread');
        });

        /**
         * Clients
         */
        Route::group(['prefix' => 'client', 'as' => 'client.'], function() {
            Route::get('/',                     'CustomerController@index')->name('index');
            Route::get('/{clientId}',           'CustomerController@show')->name('show')->where('clientId', '[0-9]+');
            Route::post('/create',              'CustomerController@create')->name('create');
            Route::put('/update',               'CustomerController@update')->name('update');
            Route::post('/archive',               'CustomerController@archive')->name('archive');
            Route::post('/restore',               'CustomerController@restore')->name('restore');
            Route::delete('/{clientId}',        'CustomerController@destroy')->name('destroy')->where('clientId', '[0-9]+');
        });

        /**
         * Priorities
         */
        Route::group(['prefix' => 'priority', 'as' => 'priority.'], function () {
            Route::get('/',                     'PriorityController@index')->name('index');
            Route::post('create',               'PriorityController@create')->name('create');
            Route::put('update',                'PriorityController@update')->name('update');
            Route::put('update/sort',           'PriorityController@updateSortOrder')->name('update.sort_order');
            Route::delete('{priorityId}',       'PriorityController@remove')->name('remove')->where('priorityId', '[0-9]+');
        });

        /**
         * Roles
         */
        Route::group(['prefix' => 'roles'], function() {
            Route::get('/',                 'RoleController@index')->name('index');
            Route::get('/{roleId}',         'RoleController@show')->name('show')->where('roleId', '[0-9]+');
            Route::post('/create',          'RoleController@create')->name('create');
            Route::put('/update',           'RoleController@update')->name('update');
            Route::delete('/{roleId}',      'RoleController@destroy')->name('destroy')->where('roleId', '[0-9]+');
        });

        /**
         * Tenant
         */
        Route::group(['prefix' => 'tenant', 'as' => 'tenant.'], function () {
            Route::get('/',                      'TenantController@index')->name('index');
            Route::get('/own',                   'TenantController@own')->name('own');
            Route::get('/roles',                 'TenantController@roles')->name('roles');

            Route::group(['prefix' => 'members', 'as' => 'members.'], function() {
                Route::get('/',                  'TenantController@members')->name('index');
                Route::get('/login/{memberId}',  'TenantController@loginUsingMemberId')->name('login')->where('memberId', '[0-9]+');
                Route::post('/update',           'TenantController@updateMember')->name('update');
                Route::post('{memberId}/update-permissions', 'TenantController@updateMemberPermissions')->name('updatePermissions')
                    ->where('memberId', '[0-9]+');
                Route::delete('/{memberId}',     'TenantController@destroyMember')->name('destroy')->where('memberId', '[0-9]+');
            });

            Route::group(['prefix' => 'role', 'as' => 'role.'], function() {
                Route::post('/attach',           'TenantController@attach')->name('attach');
                Route::post('/detach',           'TenantController@detach')->name('detach');
            });

            Route::group(['prefix' => 'settings', 'as' => 'settings.'], function() {
                Route::get('/',           'TenantController@indexSettings')->name('index');
                Route::put('/',           'TenantController@updateSettings')->name('update');
            });

            Route::group(['prefix' => 'templates', 'as' => 'templates.'], function() {
                Route::get('/',           'TenantController@indexTemplates')->name('index');
                Route::put('/',           'TenantController@updateTemplates')->name('update');
            });
        });

        Route::group(['prefix' => 'pipeline'], function () {
            Route::get('/',                     ['as' => 'pipeline.index',   'uses' => 'PipelineController@index']);
            Route::get('/{pipelineId}',         ['as' => 'pipeline.show',    'uses' => 'PipelineController@showPipeline'])->where('pipelineId', '[0-9]+');
            Route::get('/filters',              ['as' => 'pipeline.filters', 'uses' => 'PipelineController@filters']);
            Route::post('create',               ['as' => 'pipeline.create',  'uses' => 'PipelineController@createPipeline']);
            Route::put('update',                ['as' => 'pipeline.update',  'uses' => 'PipelineController@updatePipeline']);
            Route::delete('/{pipelineId}',       ['as' => 'pipeline.destroy', 'uses' => 'PipelineController@destroyPipeline'])->where('pipelineId', '[0-9]+');

            Route::group(['prefix' => '{pipelineId}' ], function() {
                Route::get('/mails',            ['as' => 'pipeline.mails',         'uses' => 'PipelineController@mails']);

                Route::group(['prefix' => 'rule' ], function() {
                    Route::get('/{ruleId}',     ['as' => 'pipeline.rule.show',   'uses' => 'PipelineController@showRule'])->where('ruleId', '[0-9]+');
                    Route::post('create',       ['as' => 'pipeline.rule.create', 'uses' => 'PipelineController@createRule']);
                    Route::put('update',        ['as' => 'pipeline.rule.update', 'uses' => 'PipelineController@updateRule']);
                    Route::delete('/{ruleId}',  ['as' => 'pipeline.rule.destroy','uses' => 'PipelineController@destroyRule'])->where('ruleId', '[0-9]+');
                });
            });
        });

        Route::get('invite',  ['as' => 'auth.getInvite', 'uses' => 'AuthController@getInvite']);
        Route::post('invite', ['as' => 'auth.invite',    'uses' => 'AuthController@invite']);

        Route::group(['prefix' => 'profile'], function () {
            Route::get('/',                ['as' => 'profile.show',           'uses' => 'UserProfileController@show']);
            Route::get('languages',        ['as' => 'profile.languages',      'uses' => 'UserProfileController@languages']);
            Route::get('fonts',            ['as' => 'profile.fonts',          'uses' => 'UserProfileController@fonts']);
            Route::post('change-password', ['as' => 'profile.changePassword', 'uses' => 'UserProfileController@changePassword']);
            Route::post('update',          ['as' => 'profile.update',         'uses' => 'UserProfileController@updateProfile']);
        });

        // Push Subscriptions TODO refactor this
        Route::post('subscriptions', 'PushSubscriptionController@update');
        Route::post('subscriptions/send-welcome', 'PushSubscriptionController@sendWelcome');
        Route::post('subscriptions/delete', 'PushSubscriptionController@destroy');
        //


        Route::group(['prefix' => 'user'], function () {
            Route::post('update',          ['as' => 'user.update',            'uses' => 'UserProfileController@updateUser']);
            Route::resource('color-scheme', 'UserColorSchemeController')->only(['store', 'update', 'destroy']);
        });

        Route::group(['prefix' => 'permission'], function() {
            Route::get('all',     ['as' => 'permission.all',     'uses' => 'PermissionController@getAvailablePermissions']);
            Route::get('get-own', ['as' => 'permission.get-own', 'uses' => 'PermissionController@getOwnPermissions']);
        });

        Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
            Route::get('/boards',     'ReportsController@boards')->name('boards');
            Route::get('/groups',     'ReportsController@groups')->name('groups');

            Route::post('/filter',    'ReportsController@filter')->name('filter');

            Route::any('/export',    'ReportsController@export')->name('filter');
        });

        Route::group(['prefix' => 'manage'], function() {
            Route::get('users', ['as' => 'manage.users', 'uses' => 'TenantController@members']);
            Route::get('available-roles',  ['as' => 'manage.available-roles', 'uses' => 'TenantController@availableRoles']);

            Route::group(['prefix' => 'role'], function() {
                Route::post('attach', ['as' => 'group.member.attach', 'uses' => 'TaskController@attach']);
                Route::post('detach', ['as' => 'group.member.detach', 'uses' => 'TaskController@detach']);
            });
        });

        Route::group(['prefix' => 'billing', 'as' => 'billing.'], function() {
            Route::get('/year/{year?}',     'TimerBillingController@getYearOverview')->name('year');
            Route::get('/status/all',       'TimerBillingController@billingStatuses')->name('status');
            Route::put('/status/update',    'TimerBillingController@update')->name('status.update');
            Route::put('/statuses/update',  'TimerBillingController@massStatusUpdate')->name('statuses.update');
        });

        Route::group(['prefix' => 'bill', 'as' => 'bill.'], function() {
            Route::get('/filter/criteria', ['as' => 'filter.criteria', 'uses' => 'BillController@getBillFilterCriteria']);
            Route::get('/filter', ['as' => 'filter', 'uses' => 'BillController@filter']);
            Route::post('/create', ['as' => 'create', 'uses' => 'BillController@create']);
            Route::get('/edit/{billId}', ['as' => 'getEdit', 'uses' => 'BillController@getEditBill']);
            Route::post('/edit', ['as' => 'edit', 'uses' => 'BillController@editBill']);
            Route::get('/add', ['as' => 'getAdd', 'uses' => 'BillController@getAddBill']);
            Route::post('/add', ['as' => 'add', 'uses' => 'BillController@addBill']);
            Route::delete('/delete/{billId}', ['as' => 'delete', 'uses' => 'BillController@delete']);
            Route::get('/list', ['as' => 'list', 'uses' => 'BillController@getBillList']);

            Route::get('/{billId}/download', 'BillController@downloadPdf')->name('download')->where('billId', '[0-9]+');
            Route::get('/{billId}/logs', 'BillController@logs')->name('logs')->where('billId', '[0-9]+');
        });

        Route::group(['prefix' => 'search'], function() {
            Route::post('', ['as' => 'search.index', 'uses' => 'SearchController@search']);
        });

        Route::group(['prefix' => 'tags'], function() {
            Route::get('/', 'TagController@index')->name('tags.index');
            Route::get('{tag}', 'TagController@show')->name('tags.show');
        });

        Route::group(['prefix' => 'device-token'], function() {
            Route::post('/', 'DeviceTokenController@store');
            Route::delete('{token}', 'DeviceTokenController@destroy');
        });

        /**
         * Reviews
         */
        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::post('/create', 'ReviewController@create');
        });
    });
});
