<?php

namespace App\Http\Resources;

class TagsCollectionResource extends BaseCollectionResource
{
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->resource->getCollection()->transform(function ($value) {
            return new TagResource($value);
        });
    }
}
