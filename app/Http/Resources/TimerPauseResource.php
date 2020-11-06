<?php

namespace App\Http\Resources;

/**
 * Class TimerPauseResource
 *
 * @package App\Http\Resources
 *
 * @property int    $id
 * @property string $start_time
 * @property string $end_time
 */
class TimerPauseResource extends BaseResource
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
            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,
        ];
    }
}