<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OptimizationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @throws Throwable
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('fields', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unsignedInteger('font_id')->default(NULL)->change();
            $table->dropForeign(['font_id']);

            $table->dropUnique('user_profiles_font_id_foreign');

            $table->foreign('font_id')->references('id')
                ->on('fields')->onDelete('cascade');
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->unsignedInteger('view_type_id')->default(NULL)->change();
            $table->dropForeign(['view_type_id']);

            $table->dropUnique('boards_view_type_id_foreign');

            $table->foreign('view_type_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['budget_type_id']);

            $table->dropUnique('budgets_budget_type_id_foreign');

            $table->foreign('budget_type_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('notification_type_user', function (Blueprint $table) {
            $table->dropForeign(['notification_type_id']);

            $table->dropUnique('notification_type_user_notification_type_id_foreign');

            $table->foreign('notification_type_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');
        });

        Artisan::call('db:seed', ['--force' => true, '--class' => 'NotificationTypesSeeder']);
        Artisan::call('db:seed', ['--force' => true, '--class' => 'BudgetTypesSeeder']);
        Artisan::call('db:seed', ['--force' => true, '--class' => 'FontsSeeder']);
        Artisan::call('db:seed', ['--force' => true, '--class' => 'ViewTypesSeeder']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::dropIfExists('fonts');
        Schema::dropIfExists('view_types');
        Schema::dropIfExists('budget_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
