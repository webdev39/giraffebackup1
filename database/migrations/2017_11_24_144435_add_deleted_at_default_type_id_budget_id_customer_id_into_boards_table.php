<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtDefaultTypeIdBudgetIdCustomerIdIntoBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('view_type_id')->default(1);
            $table->unsignedInteger('budget_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->boolean('is_archive')->default(0);
            $table->dropColumn('budget');

            $table->foreign('view_type_id')->references('id')->on('view_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('budget_id')->references('id')->on('budgets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropForeign(['view_type_id']);
            $table->dropForeign(['budget_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('deleted_at');
            $table->dropColumn('view_type_id');
            $table->dropColumn('budget_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('is_archive');
            $table->integer('budget');
        });
    }
}
