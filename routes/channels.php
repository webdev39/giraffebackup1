<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\Group;
use App\Models\User;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('communication.{groupId}.user.{id}', function(User $user, $groupId) {
    return $user->can('show', Group::findOrFail($groupId));
});

Broadcast::channel('tasks', function (User $user) {
    return true;
});
