<?php

namespace App\Collections;

use Illuminate\Support\Collection;

/**
 * Class UserTenantCollection
 *
 * @package App\Collections
 */
class UserTenantCollection extends BaseCollection
{
    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function setUsers(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($user = $collection->where('id', $value['user_id'])->first()) {
                $value['user'] = self::getElement($user);
            }

            return $value;
        });
    }

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function setRoles(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($roles = $collection->where('user_tenant_id', $value['id'])) {
                $value['roles'] = $roles;
            }

            return $value;
        });
    }

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function setUserTenantGroups(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($userTenantGroups = $collection->get($value['id'])) {
                $value['user_tenant_groups'] = $userTenantGroups;
            }

            return $value;
        });
    }
}