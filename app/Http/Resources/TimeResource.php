<?php

namespace App\Http\Resources;

/**
 * Class TimeResource
 *
 * @package App\Http\Resources
 *
 * @property integer $m
 * @property integer $d
 * @property integer $h
 * @property integer $i
 * @property integer $s
 */
class TimeResource extends BaseResource
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
            'h' => (integer) self::getHours($this),
            'i' => (integer) $this->i,
            's' => (integer) $this->s,
        ];
    }

    /**
     * @param $obj
     *
     * @return int
     */
    public static function getHours($obj) : int
    {
        return $obj->h + $obj->days * 24;
    }
}