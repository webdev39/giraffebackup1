<?php

namespace App\Http\Resources;

/**
 * Class ViewTypesResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $description
 * @property string     $alias
 * @property string     $color
 */
class FieldResource extends BaseResource
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
            'id'    => $this->id,
            'name'  => $this->name,
        ];

        if (isset($this->description)) {
            $result['description'] = $this->description;
        }

        if (isset($this->alias)) {
            $result['alias'] = $this->alias;
        }

        if (isset($this->color)) {
            $result['color'] = $this->color;
        }

        return $result;
    }
}
