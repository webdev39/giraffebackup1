<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;

/**
 * Class UserTenantGroupResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property integer    $group_id
 * @property \stdClass  $group
 * @property Collection $roles
 */
class UserTenantGroupResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        $result = [
            'group_id'    => $this->group_id,
            'role_id'     => $this->roles->id
        ];

        return $result;
    }
}
