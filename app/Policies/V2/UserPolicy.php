<?php

namespace App\Policies\V2;

use App\Models\User;
use App\Models\UserTenantGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
}
