<?php

namespace App\Collections;

use Illuminate\Support\Collection;

/**
 * Class GroupCollection
 *
 * @package App\Collections
 */
class GroupCollection extends BaseCollection
{
    /** @var Collection */
    public $permissions;

    /**
     * @return static
     */
    public function getActiveGroups()
    {
        return $this->where('is_archive', false)->values();
    }

    /**
     * @return static
     */
    public function getArchivedGroups()
    {
        return $this->where('is_archive', true)->values();
    }

    /**
     * @param Collection $permissions
     *
     * @return GroupCollection
     */
    public function setPermissions(Collection $permissions)
    {
        $this->permissions = $permissions->unique('id');
        $groupPermissions  = $permissions->groupBy('group_id');

        return $this->mutator(function(array $value) use ($groupPermissions)
        {
//            if ($result = self::getElement($groupPermissions, $value['id'])) {
//                $result = $result->unique('id');
//            }

            $value['permissions'] = self::getElement($groupPermissions, $value['id']);

            return $value;
        });
    }
}