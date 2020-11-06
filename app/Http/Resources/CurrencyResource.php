<?php

namespace App\Http\Resources;

/**
 * Class CountryResource
 *
 * @package App\Http\Resources
 */
class CurrencyResource extends BaseResource
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
            'id'    => $this->id,
            'name'  => $this->name,
            'code'  => $this->code,
            'symbol'=> $this->symbol,
        ];
    }
}
