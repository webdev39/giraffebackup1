<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\UserTenant;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function getOwnPermissions()
    {
        $permissions = app('PermissionSer')->getPermissionsByUserId(Auth::id());
        return response()->json(['message' => 'User permissions', 'permissions' => $permissions]);
    }

    public function getAvailablePermissions()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        if (!$userTenant->hasRole(Role::ADMIN_ROLE['name'] || !$userTenant->hasRole([Role::OWNER_ROLE['name']]))) {
            return response()->json(['message' => 'User has no permissions to get this data'], 403);
        }
        $permissions = app('PermissionSer')->getAvailablePermissions();
        return response()->json(['message' => 'Available Tenant permissions', 'permissions' => $permissions]);
    }
}
