<?php

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypesSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $notificationEmail = \App\Models\Field::create(['type' => 'notification_type', 'key' => 'email', 'name' => 'Email', 'is_public' => 1, 'is_default' => 1]);

            \App\Models\NotificationTypeUser::where('notification_type_id', 1)->update(['notification_type_id' => $notificationEmail->id]);
        });
    }
}
