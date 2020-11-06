<?php

use Illuminate\Database\Seeder;

class BudgetTypesSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $budgetOne = \App\Models\Field::create(['type' => 'budget_type', 'key' => 'one_time', 'name' => 'One-Time', 'is_public' => 1, 'is_default' => 1]);
            $budgetMonth = \App\Models\Field::create(['type' => 'budget_type', 'key' => 'monthly', 'name' => 'Monthly', 'is_public' => 1]);

            \App\Models\Budget::where('budget_type_id', 1)->update(['budget_type_id' => $budgetOne->id]);
            \App\Models\Budget::where('budget_type_id', 2)->update(['budget_type_id' => $budgetMonth->id]);
        });
    }
}
