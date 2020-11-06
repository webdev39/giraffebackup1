<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function run()
    {
        $json = File::get("database/data/countries.json");
        $data = json_decode($json);

        foreach ($data as $obj) {
            Country::create([
                'name'          => $obj->name,
                'full_name'     => $obj->full_name,
                'capital'       => $obj->capital,
                'citizenship'   => $obj->citizenship,
                'iso_3166_2'    => $obj->iso_3166_2,
                'iso_3166_3'    => $obj->iso_3166_3,
                'country_code'  => $obj->country_code,
                'currency_code' => $obj->currency_code,
                'calling_code'  => $obj->calling_code,
                'eea'           => $obj->eea
            ]);
        }
    }
}
