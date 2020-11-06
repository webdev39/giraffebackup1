<?php

use App\Models\Field;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGanttViewType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Field::where(['type' => 'view_type', 'key' => 'gantt'])->count()) {
            Field::create(['type' => 'view_type', 'key' => 'gantt', 'name' => 'Gantt', 'is_public' => 1]);
        }
    }
}
