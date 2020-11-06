<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFontsToFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $newFonts = [
            [
                'type' => 'font',
                'key' => 'poppins',
                'name' => 'Poppins',
                'is_public' => 1,
                'is_default' => 0
            ], [
                'type' => 'font',
                'key' => 'montserrat',
                'name' => 'Montserrat',
                'is_public' => 1,
                'is_default' => 0
            ], [
                'type' => 'font',
                'key' => 'nunito_sans',
                'name' => 'Nunito Sans',
                'is_public' => 1,
                'is_default' => 0
            ], [
                'type' => 'font',
                'key' => 'open_sans',
                'name' => 'Open Sans',
                'is_public' => 1,
                'is_default' => 0
            ], [
                'type' => 'font',
                'key' => 'lato',
                'name' => 'Lato',
                'is_public' => 1,
                'is_default' => 0
            ]
        ];

        Schema::table('fields', function (Blueprint $table) use ($newFonts)  {
            DB::table('fields')
                ->insert($newFonts);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            //
        });
    }
}
