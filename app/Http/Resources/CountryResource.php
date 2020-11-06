<?php

namespace App\Http\Resources;

/**
 * Class CountryResource
 *
 * @package App\Http\Resources
 */
class CountryResource extends BaseResource
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
            'calling_code'  => $this->calling_code,
            'currency_code' => $this->currency_code,
            'country_code'  => $this->country_code,
            'iso_3166_2'    => $this->iso_3166_2,
        ];
    }
}
