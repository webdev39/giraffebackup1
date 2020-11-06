<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Customer $customer)
    {
        $userTenant = $user->getChosenTenant();
        if ($userTenant->tenant_id !== $customer->tenant_id) {
            return response()->json(['message' => 'Client is not in Tenant'], 403);
        }
        Auth::failIfHasNoPermission(Permission::MANAGE_CUSTOMERS_PERMISSION);
        return true;
    }

    public function destroy(User $user, Customer $customer)
    {
        $userTenant = $user->getChosenTenant();
        if ($userTenant->tenant_id !== $customer->tenant_id) {
            return response()->json(['message' => 'Client is not in Tenant'], 403);
        }
        Auth::failIfHasNoPermission(Permission::MANAGE_CUSTOMERS_PERMISSION);
    }
}
