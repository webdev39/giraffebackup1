<?php

namespace App\Http\Resources;

use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class BaseResource
 *
 * @package App\Http\Resources
 *
 * @property integer $group_id
 * @property integer $user_tenant_id
 */
abstract class BaseResource extends Resource
{
    /** @var UserTenant $userTenantPermission */
    private $userTenantPermission;

    /** @var UserTenantGroup $userTenantGroupPermission */
    private $userTenantGroupPermission;

    /**
     * @param $permission
     *
     * @return bool
     */
    protected function checkPermission($permission)
    {
        if (!$this->userTenantPermission) {
            $this->userTenantPermission = Auth::userTenant();
        }

        if ($this->user_tenant_id == Auth::userTenantId()) {
            return true;
        }

        if (!$this->userTenantGroupPermission) {
            $this->userTenantGroupPermission = Auth::userTenantGroup($this->group_id);
        }

        return optional($this->userTenantPermission)->can($permission)
            || optional($this->userTenantGroupPermission)->can($permission);
    }

    /**
     * Return the values from a single column in the input collection
     *
     * @param $collection
     * @param $column
     *
     * @return array
     */
    function getCollectionColumn($collection, $column)
    {
        return $collection instanceof \Illuminate\Support\Collection ? array_column($collection->toArray(), $column) : null;
    }
}