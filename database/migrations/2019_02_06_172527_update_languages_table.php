<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLanguagesTable extends Migration
{
    /**
     * @throws Throwable
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('languages');

        Schema::create('languages', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('iso_639_1');
            $table->boolean('is_local')->default(false);
        });

        Artisan::call('db:seed', ['--force' => true, '--class' => 'LanguagesSeeder']);

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unsignedInteger('language_id')->default(NULL)->change();
            $table->dropForeign(['language_id']);

            $table->dropUnique('user_profiles_language_id_foreign');

            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });

        \Illuminate\Support\Facades\DB::transaction(function () {
            $langEn = \App\Models\Language::where('iso_639_1','en')->get()->first();
            $langDe = \App\Models\Language::where('iso_639_1','de')->get()->first();

            $langEn->update(['is_local' => true]);
            $langDe->update(['is_local' => true]);

            \App\Models\UserProfile::where('language_id', 1)->update(['language_id' => $langEn->id]);
            \App\Models\UserProfile::where('language_id', 2)->update(['language_id' => $langDe->id]);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
