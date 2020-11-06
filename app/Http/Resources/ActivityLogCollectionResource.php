<?php

namespace App\Http\Resources;

/**
 * Class ActivityLogCollectionResource
 *
 * @package App\Http\Resources
 */
class ActivityLogCollectionResource extends BaseCollectionResource
{
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->resource->getCollection()->transform(function ($value) {
            return new ActivityLogResource($value);
        });
    }
}
