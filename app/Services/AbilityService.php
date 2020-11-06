<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\PermissionNotFoundException;
use App\HasPermissions;

/**
 * Class PermissionsService
 * @package App\Services
 */
class AbilityService
{
    /**
     * @param string $permissionCode
     * @param HasPermissions $permissible
     * @return bool
     * @throws PermissionNotFoundException
     */
    public function hasPermission(string $permissionCode, HasPermissions $permissible): bool
    {
        return $permissible->can($this->findPermission($permissionCode)['name']);
    }

    /**
     * @param array $permissions
     * @param HasPermissions $permissible
     * @return bool
     * @throws PermissionNotFoundException
     */
    public function hasPermissions(array $permissions, HasPermissions $permissible): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $permissible)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $code
     * @return array
     * @throws PermissionNotFoundException
     */
    public function findPermission(string $code): array
    {
        $fullCode = $code . '_PERMISSION';
        if (!defined('App\Models\Permission::' . $fullCode)) {
            throw new PermissionNotFoundException($code);
        }

        return constant('App\Models\Permission::' . $fullCode);
    }
}