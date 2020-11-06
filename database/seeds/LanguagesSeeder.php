<?php

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function run()
    {
        $json = File::get("database/data/languages.json");
        $data = json_decode($json);

        foreach ($data as $obj) {
            Language::create([
                'name'      => $obj->name,
                'iso_639_1' => $obj->iso_639_1,
            ]);
        }
    }
}
