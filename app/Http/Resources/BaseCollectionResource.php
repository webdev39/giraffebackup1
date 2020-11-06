<?php

namespace App\Http\Resources;

use App\Models\UserTenant;
use App\Models\UserTenantGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class BaseResource
 *
 * @package App\Http\Resources
 *
 * @method int count()
 * @method int currentPage()
 * @method int perPage()
 * @method int total()
 * @method int lastPage()
 */
abstract class BaseCollectionResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            return [
                'data'          => $this->getData(),
                'pagination'    => $this->getPagination(),
            ];
        }

        return $this->getData();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->resource->getCollection()->transform(function ($value) {
            return new ActivityLogResource($value);
        });
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
            'total'         => $this->total(),
            'total_pages'   => $this->lastPage(),
        ];
    }
}