<?php

namespace App\Http\Resources;

/**
 * Class CountryResource
 *
 * @package App\Http\Resources
 */
class LanguageResource extends BaseResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'iso_639_1' => $this->iso_639_1,
            'is_local'  => $this->is_local,
        ];
    }
}
