<?php

namespace App\Http\Resources;

/**
 * Class PriorityResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property integer    $board_id
 * @property string     $name
 * @property string     $description
 * @property string     $color
 * @property integer    $sort_order
 * @property integer    $is_default
 * @property integer    $is_primary
 * @property integer    $is_invisible
 */
class PriorityResource extends BaseResource
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
            'description'       => $this->description,
            'color'             => $this->color,
            'board_id'          => $this->board_id,
            'sort_order'        => $this->sort_order,
            'is_default'        => (bool) $this->is_default,
            'is_primary'        => (bool) $this->is_primary,
            'is_invisible'      => (bool) $this->is_invisible,
        ];
    }
}
