<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;

class PipelineResource extends BaseResource
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

        $result = [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'description'   => $this->description,
            'host'          => $this->host,
            'port'          => $this->port,
            'encryption'    => $this->encryption,
            'password'      => $this->password,
            'is_active'     => $this->is_active,
            'rules'         => [],
        ];

        if (isset($this->rules)) {
            $result['rules'] = PipelineRuleResource::collection($this->rules);
        }

        return $result;
    }
}
