<?php
/**
 * Created by PhpStorm.
 * User: nikolaygolub
 * Date: 04.12.2017
 * Time: 15:01
 */

namespace App\Services\CustomAuth;


use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Database\Eloquent\Model;

interface CustomSessionGuardInterface
{
    public function userTenant(): UserTenant;

    public function userTenantGroup($groupId);

    public function failIfHasNoPermissionsOnTenantLevel(
        UserTenant $userTenant,
        UserTenantGroup $userTenantGroup,
        array  $permissions
    );

    public function failIfHasNoPermission(array $permissionName, Model $entity = null);

    public function failIfHasNoRole(string $role, Model $entity = null);
}
