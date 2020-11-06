<?php

namespace App\Http\Resources;

/**
 * Class FilterResource
 *
 * @package App\Http\Resources

 * @property int        $id
 * @property string     $name
 *
 * @property string     $range
 * @property array      $assigner_ids
 * @property array      $group_ids
 * @property array      $board_ids
 * @property array      $priority_ids
 * @property int        $view_type_id
 * @property int        $status
 */
class FilterResource extends BaseResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'range'         => $this->range,
            'assigner_ids'  => $this->assigner_ids,
            'group_ids'     => $this->group_ids,
            'board_ids'     => $this->board_ids,
            'priority_ids'  => $this->priority_ids,
            'view_type_id'  => $this->view_type_id,
            'status'        => $this->status,
        ];
    }
}
