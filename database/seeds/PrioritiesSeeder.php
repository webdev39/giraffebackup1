<?php

use Illuminate\Database\Seeder;

class PrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Priority::updateOrCreate([
            'id'            => 1,
        ],[
            'name'          => 'High',
            'description'   => 'High priority',
            'color'         => 'red',
            'is_default'    => 1,
            'is_primary'    => 0
        ]);

        \App\Models\Priority::updateOrCreate([
            'id'            => 2,
        ],[
            'name'          => 'Normal',
            'description'   => 'Normal priority',
            'color'         => 'black',
            'is_default'    => 1,
            'is_primary'    => 1,
        ]);

        \App\Models\Priority::updateOrCreate([
            'id'            => 3,
        ],[
            'name'          => 'Low',
            'description'   => 'Low priority',
            'color'         => 'green',
            'is_default'    => 1,
            'is_primary'    => 0,
        ]);
    }
}
