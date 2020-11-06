<?php

use App\Models\BillingStatus;
use Illuminate\Database\Seeder;

class UpdateBillingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BillingStatus::where('id', 1)->update(BillingStatus::INITIAL_STATUSES['Open']);
        BillingStatus::where('id', 2)->update(BillingStatus::INITIAL_STATUSES['Unknown']);
        BillingStatus::where('id', 3)->update(BillingStatus::INITIAL_STATUSES['Parked']);
        BillingStatus::where('id', 4)->update(BillingStatus::INITIAL_STATUSES['Billed']);
    }
}
