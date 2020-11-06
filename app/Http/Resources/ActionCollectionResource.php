<?php

namespace App\Http\Resources;

class ActionCollectionResource extends BaseCollectionResource
{
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->resource->getCollection()->transform(function ($value) {
            return new ActionResource($value);
        })->values();
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return [
            'count'         => $this->count(),
            'current_page'  => $this->currentPage(),
            'per_page'      => $this->perPage(),
            'total'         => null,
            'total_pages'   => null,
        ];
    }
}
