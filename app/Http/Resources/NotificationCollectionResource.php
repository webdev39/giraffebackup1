<?php

namespace App\Http\Resources;

class NotificationCollectionResource extends BaseCollectionResource
{
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->resource->getCollection()->transform(function ($value) {
            return new NotificationResource($value);
        });
    }
}
