<?php

use Illuminate\Database\Seeder;

class SortingWeightTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->get()->each(function($row) {
            DB::table('tasks')
                ->where('id', $row->id)
                ->update([
                    'sort_weight' => time() + rand(0, 60 * 60 * 24),
                ]);
        });
    }
}

