<?php

use Illuminate\Database\Seeder;

class PipelineFiltersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\PipelineFilter::insert(
            [
                ['is_active' => 1, 'name' => 'from',            'display_name' => 'Sender'],
                ['is_active' => 1, 'name' => 'subject',         'display_name' => 'Subject'],
                ['is_active' => 0, 'name' => 'attachment_type', 'display_name' => 'Attachment type'],
                ['is_active' => 1, 'name' => 'body',            'display_name' => 'Content'],
            ]
        );
    }
}
