<?php

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function run()
    {
        $json = File::get("database/data/currencies.json");
        $data = json_decode($json);

        foreach ($data as $obj) {
            Currency::create([
                'name'      => ucfirst($obj->name),
                'code'      => $obj->code,
                'sub_unit'  => $obj->sub_unit,
                'symbol'    => $obj->symbol,
            ]);
        }
    }
}
