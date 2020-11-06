<?php

use Illuminate\Database\Seeder;

class BillLayoutTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BillLayoutType::insert([
            \App\Models\BillLayoutType::BILL_LAYOUT_TYPES['Short'],
            \App\Models\BillLayoutType::BILL_LAYOUT_TYPES['Long']
        ]);
    }
}
