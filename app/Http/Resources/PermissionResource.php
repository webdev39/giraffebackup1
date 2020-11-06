<?php

namespace App\Http\Resources;

/**
 * Class PermissionResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $display_name
 * @property string     $description
 */
class PermissionResource extends BaseResource
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

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'display_name'      => $this->display_name,
            'description'       => $this->description,
        ];
    }
}
