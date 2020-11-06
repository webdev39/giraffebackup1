<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;

/**
 * Class UserTenantResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property integer    $tenant_id
 * @property boolean    $is_owner
 * @property \stdClass  $role
 * @property Collection $user_tenant_group
 * @property Collection $user
 */
class UserTenantResource extends BaseResource
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
            'tenant_id'         => $this->tenant_id,
            'company_name'      => self::getCompanyName($this),
            'is_owner'          => (boolean) $this->is_owner,
        ];

        if (isset($this->user)) {
            $result['user'] = new UserResource($this->user);
        }

        if (isset($this->roles)) {
            $result['roles'] = [];
            foreach ($this->roles as $role) {
                $result['roles'][] = new RoleResource($role);
            }
        }

        if ($request->get('user_tenant_res') == 'long') {
            if (isset($this->user_tenant_groups)) {
                $result['group_role'] = UserTenantGroupResource::collection($this->user_tenant_groups);
            } else {
                $result['group_role'] = [];
            }
        }

        return $result;
    }

    /**
     * @param $tenant
     *
     * @return mixed
     */
    private static function getCompanyName($tenant)
    {
        return $tenant->company_name ?? $tenant->tenant_company_name ?? $tenant->tenant_project_name;
    }
}
