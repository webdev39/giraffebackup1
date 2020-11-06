<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('board_task', function (Blueprint $table) {
            $table->index(['board_id', 'task_id']);
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->index(['group_id', 'is_archive']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['tenant_id', 'status']);
        });

        Schema::table('notification_subscriptions', function (Blueprint $table) {
            $table->index(['task_id', 'user_id']);
        });

        Schema::table('pauses', function (Blueprint $table) {
            $table->index('end_time');

            $table->index(['timer_id', 'end_time']);
        });

        Schema::table('personal_deadlines', function (Blueprint $table) {
            $table->index('planned_deadline');

            $table->index(['user_tenant_id', 'task_id']);
        });

        Schema::table('priorities', function (Blueprint $table) {
            $table->index('is_default');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index('is_default');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('draft');
            $table->index('is_archive');
        });

        Schema::table('tenant_custom_role', function (Blueprint $table) {
            $table->index(['tenant_id', 'role_id']);
        });

        Schema::table('timers', function (Blueprint $table) {
            $table->index('end_time');
            $table->index('start_time');

            $table->index(['start_time', 'end_time']);
            $table->index(['user_tenant_id', 'task_id']);
        });

        Schema::table('user_tenant_group_role', function (Blueprint $table) {
            $table->index(['user_tenant_group_id', 'role_id']);
        });

        Schema::table('user_tenant_task', function (Blueprint $table) {
            $table->index(['user_tenant_id', 'task_id']);
        });

        Schema::table('user_tenant_group', function (Blueprint $table) {
            $table->index(['user_tenant_id', 'group_id']);
        });

        Schema::table('user_tenant', function (Blueprint $table) {
            $table->index('is_owner');
            $table->index('invite_hash');

            $table->index(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'is_owner']);
            $table->index(['tenant_id', 'is_owner', 'invite_hash']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['email', 'is_confirmed']);
            $table->index(['id', 'chosen_tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_task', function (Blueprint $table) {
            $table->dropIndex(['board_id', 'task_id']);
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->dropIndex(['group_id', 'is_archive']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
        });

        Schema::table('notification_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['task_id', 'user_id']);
        });

        Schema::table('pauses', function (Blueprint $table) {
            $table->dropIndex('end_time');

            $table->dropIndex(['timer_id', 'end_time']);
        });

        Schema::table('personal_deadlines', function (Blueprint $table) {
            $table->dropIndex('planned_deadline');

            $table->dropIndex(['user_tenant_id', 'task_id']);
        });

        Schema::table('priorities', function (Blueprint $table) {
            $table->dropIndex('is_default');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('is_default');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('draft');
            $table->dropIndex('is_archive');
        });

        Schema::table('tenant_custom_role', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'role_id']);
        });

        Schema::table('timers', function (Blueprint $table) {
            $table->dropIndex('end_time');
            $table->dropIndex('start_time');

            $table->dropIndex(['start_time', 'end_time']);
            $table->dropIndex(['user_tenant_id', 'task_id']);
        });

        Schema::table('user_tenant_group_role', function (Blueprint $table) {
            $table->dropIndex(['user_tenant_group_id', 'role_id']);
        });

        Schema::table('user_tenant_task', function (Blueprint $table) {
            $table->dropIndex(['user_tenant_id', 'task_id']);
        });

        Schema::table('user_tenant_group', function (Blueprint $table) {
            $table->dropIndex(['user_tenant_id', 'group_id']);
        });

        Schema::table('user_tenant', function (Blueprint $table) {
            $table->dropIndex('is_owner');
            $table->dropIndex('invite_hash');

            $table->dropIndex(['tenant_id', 'user_id']);
            $table->dropIndex(['tenant_id', 'is_owner']);
            $table->dropIndex(['tenant_id', 'is_owner', 'invite_hash']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email', 'is_confirmed']);
            $table->dropIndex(['id', 'chosen_tenant_id']);
        });
    }
}
