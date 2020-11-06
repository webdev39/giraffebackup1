<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class ActivityLogResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $log_name
 * @property string     $description
 * @property string     $task_name
 * @property Carbon     $updated_at
 * @property Carbon     $created_at
 * @property Collection $properties
 */
class ActivityLogResource extends BaseResource
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

        return array_merge($this->properties->toArray(), [
            'id'            => $this->id,
            'log_name'      => $this->log_name,
            'body'          => $this->description,
            'updated_at'    => (string) $this->updated_at,
            'created_at'    => (string) $this->created_at,
            'task_name'     => $this->task_name ?? '',
            'created_at_diff_for_humans' => ($this->created_at instanceof Carbon ? $this->created_at : Carbon::parse($this->created_at))->diffForHumans(),
        ]);
    }
}
