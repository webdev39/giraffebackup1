<?php

namespace App\Policies;

use App\Models\Filter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class FilterPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Filter $filter)
    {
        if ($filter->user_tenant_id != $user->getChosenTenant()->id) {
            abort(403, 'User has no access to this filter');
        }
        return true;
    }
}
