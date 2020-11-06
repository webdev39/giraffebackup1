<?php

namespace App\Policies\V2;

use App\Models\User;
use App\Models\UserColorScheme;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserColorSchemePolicy
{
    use HandlesAuthorization;

    public function update(User $user, UserColorScheme $userColorScheme)
    {
        return $userColorScheme->isOwnedBy($user);
    }

    public function destroy(User $user, UserColorScheme $userColorScheme)
    {
        return $userColorScheme->isOwnedBy($user);
    }
}
