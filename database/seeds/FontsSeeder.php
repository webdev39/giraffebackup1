<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class FontsSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $fontArial = \App\Models\Field::create(['type' => 'font', 'key' => 'helvetica', 'name' => 'Helvetica', 'is_public' => 1, 'is_default' => 1]);
            $fontCourier = \App\Models\Field::create(['type' => 'font', 'key' => 'courier', 'name' => 'Courier New', 'is_public' => 1]);
            $fontTimes = \App\Models\Field::create(['type' => 'font', 'key' => 'times', 'name' => 'Times New Roman', 'is_public' => 1]);

            \App\Models\UserProfile::where('font_id', 1)->update(['font_id' => $fontArial->id]);
            \App\Models\UserProfile::where('font_id', 2)->update(['font_id' => $fontTimes->id]);
        });
    }
}
