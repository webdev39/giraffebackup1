<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;

/**
 * Class GroupResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $description
 * @property boolean    $is_archive
 * @property Collection $boards
 * @property Collection $members
 * @property Collection $permissions
 */
class GroupResource extends BaseResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'is_archive'        => (bool) $this->is_archive,
        ];

        if ($request->get('group_res') != 'short') {
            if (isset($this->boards)) {
                $result['boards'] = BoardResource::collection($this->boards);
            }

            if (isset($this->members)) {
                $result['members'] = array_values(array_unique($this->getCollectionColumn($this->members, 'id')));
            }

            if (isset($this->permissions)) {
                $result['permissions'] = $this->getCollectionColumn($this->permissions, 'id');
            }
        }

        return $result;
    }
}