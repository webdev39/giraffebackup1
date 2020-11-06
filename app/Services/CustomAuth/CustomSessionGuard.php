<?php

namespace App\Services\CustomAuth;

use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Auth\SessionGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomSessionGuard extends SessionGuard implements CustomSessionGuardInterface
{

    /**
     * @return UserTenant
     */
    public function userTenant() : UserTenant
    {
        /** @var User $user */
        $user = $this->user ?? User::find(request()->userId);

        if(!Auth::id()) {
            return false;
        }

        return $user->getChosenTenant();
    }

    /**
     * @return int
     */
    public function utcOffset() : int
    {
        /** @var User $user */
        $user = $this->user ?? (new User)->find(request()->get('user_id'));

        if ($user && $user->utc_offset > 0) {
            return $user->utc_offset;
        }

        return (int) request()->header('utc-offset', 0);
    }

    /**
     * @return int
     */
    public function userTenantId() : int
    {
        return $this->userTenant()->id;
    }

    /**
     * @param $groupId
     *
     * @return mixed
     */
    public function userTenantGroup($groupId)
    {
        return app('UserTenantGroupRepo')->findWhere(
            [
                'user_tenant_id' => $this->userTenantId(),
                'group_id'       => $groupId
            ]
        )->first();
    }

    /**
     * @param \App\Models\UserTenant        $userTenant
     * @param \App\Models\UserTenantGroup   $userTenantGroup
     * @param array                         $permissions
     *
     * @return bool
     */
    public function failIfHasNoPermissionsOnTenantLevel(UserTenant $userTenant, UserTenantGroup $userTenantGroup, array  $permissions)
    {
        $hasPermission = false;
        $failedPermissions = [];

        foreach ($permissions as $permission) {
            if ($userTenant->can($permission['name']) || $userTenantGroup->can($permission['name'])) {
                $hasPermission = true;
                break;
            }

            $failedPermissions[] = $permission['display_name'];
        }

        if (!$hasPermission) {
            $message = count($failedPermissions) ? $this->getErrorMessage($failedPermissions) : 'You have no permissions for this action';

            abort(403, $message);
        }

        return true;
    }

    /**
     * @param Model $entity
     * @param array $permission
     *
     * @return \Illuminate\Http\JsonResponse|bool
     */
    public function failIfHasNoPermission(array $permission, Model $entity = null)
    {
        $entity = $entity ? $entity : $this->userTenant();

        if (!$entity->can($permission['name'])) {
            abort(403, $this->getErrorMessage([$permission['display_name']]));
        }

        return true;
    }

    /**
     * @param Model $entity
     * @param string $role
     * @return \Illuminate\Http\JsonResponse|bool
     */

    public function failIfHasNoRole(string $role, Model $entity = null)
    {
        $entity = $entity ? $entity : $this->userTenant();
        if (!$entity->hasRole($role)) {
            abort(403, 'You have no permissions for this action');
        }
        return true;
    }

    public function getErrorMessage(array $permissionDisplayName)
    {
        return __('permission.general', [
            'message'  => implode(', ', $permissionDisplayName)
        ]);
    }

}
