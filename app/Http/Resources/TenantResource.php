<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class TenantResource
 *
 * @package App\Http\Resources
 *
 * @property int                    $id
 * @property string|null            $company_name
 * @property string|null            $project_name
 * @property integer|null           $count_users
 * @property \Carbon\Carbon|null    $created_at
 */
class TenantResource extends Resource
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
            'name'          => $this->company_name ?? $this->project_name,
            'count_users'   => $this->count_users,
            'created_at'    => $this->created_at,
        ];
    }
}
