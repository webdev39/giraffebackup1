<?php

use App\Models\UserColorScheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultSchemesToUserColorSchemes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_color_schemes', function (Blueprint $table) {
            foreach (UserColorScheme::defaultScheme() as $scheme) {
                UserColorScheme::create($scheme);
            }

            UserColorScheme::where('default', false)->get()->each(function ($item, $key) {
                $item->update([
                    'notification' => [
                        'bg' => '#fff',
                        'color' => '#333',
                    ],
                    'font' => [
                        'color' => '#333',
                    ]
                ]);
            });

            $userColorScheme = UserColorScheme::where('default', true)->first();
            $getUserProfiles = DB::table('user_profiles')
                ->select('id', 'selected_color_scheme_id')
                ->where('selected_color_scheme_id', null)
                ->get()
                ->pluck('id')
                ->toArray();

            \App\Models\UserProfile::whereIn('id', $getUserProfiles)
                ->update(['selected_color_scheme_id' => $userColorScheme->id]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_color_schemes', function (Blueprint $table) {
            //
        });
    }
}
