<?php

namespace App\Http\Resources;

use Carbon\Carbon;

/**
 * Class SubTaskResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property int        $task_id
 * @property string     $name
 * @property int        $order
 * @property int        $is_completed
 * @property Carbon     $created_at
 */
class SubTaskResource extends BaseResource
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
            'task_id'       => $this->task_id,
            'name'          => $this->name,
            'order'         => $this->order,
            'is_completed'  => (bool) $this->is_completed,
            'created_at'    => (string) $this->created_at,
        ];
    }
}
