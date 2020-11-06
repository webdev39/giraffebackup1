<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PipelineRuleResource extends BaseResource
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
            'id'                    => $this->id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'pipeline_id'           => $this->pipeline_id,
            'pipeline_filter_id'    => $this->pipeline_filter_id,
            'boards'                => [],
            'keywords'              => $this->keywords,
        ];

        if (isset($this->boards)) {
            $result['boards'] = $this->getCollectionColumn($this->boards, 'id');
        }

        return $result;
    }
}
