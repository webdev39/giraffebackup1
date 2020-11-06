<?php

use Illuminate\Database\Seeder;

class BillingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BillingStatus::insert([
            \App\Models\BillingStatus::INITIAL_STATUSES['Open'],
            \App\Models\BillingStatus::INITIAL_STATUSES['Unknown'],
            \App\Models\BillingStatus::INITIAL_STATUSES['Parked'],
            \App\Models\BillingStatus::INITIAL_STATUSES['Billed']
        ]);
    }
}
