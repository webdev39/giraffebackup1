<?php

namespace App\Http\Resources;

/**
 * Class RoleResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $display_name
 * @property string     $description
 * @property boolean    $is_default
 * @property boolean    $is_manual
 * @property boolean    $can_invited
 */
class RoleResource extends BaseResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'display_name'      => $this->display_name,
            'description'       => $this->description,
            'is_default'        => (bool) $this->is_default,
            'is_manual'         => (bool) $this->is_manual,
        ];

        if (isset($this->perms)) {
            $result['permissions'] = $this->getCollectionColumn($this->perms, 'id');
        }

        if (isset($this->can_invited)) {
            $result['can_invited'] = (bool) $this->can_invited;
        }

        return $result;
    }
}
