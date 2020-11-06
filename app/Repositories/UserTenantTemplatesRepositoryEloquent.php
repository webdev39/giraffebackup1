<?php

namespace App\Repositories;

use App\Models\UserTenantSettings;
use App\Models\UserTenantTemplates;

/**
 * Class UserTenantSettingsRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property UserTenantSettings $model
 */
class UserTenantTemplatesRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenantTemplates::class;
    }

    /**
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getCompanyTemplatesByTenantId(int $tenantId)
    {
        return $this->model
            ->whereHas('user_tenant', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)->where('is_owner', 1);
            })
            ->get();
    }

    /**
     * @param string $type
     * @param string $key
     * @param string $content
     * @param int    $userTenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCompanyTemplate(string $type, string $key, string $content, int $userTenantId)
    {
        $attributes = [
            'key'               => $key,
            'type'              => $type,
            'user_tenant_id'    => $userTenantId,
        ];

        $model = $this->findWhere($attributes)->first();

        $attributes['content'] = $content;

        if ($model) {
            return $this->update($attributes, $model->id);
        }

        return $this->create($attributes);
    }
}
