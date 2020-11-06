<?php

namespace App\Repositories;

use App\Models\UserTenantSettings;

/**
 * Class UserTenantSettingsRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property UserTenantSettings $model
 */
class UserTenantSettingsRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTenantSettings::class;
    }

    /**
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getCompanySettingsByTenantId(int $tenantId)
    {
        return $this->model
            ->whereHas('user_tenant', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)->where('is_owner', 1);
            })
            ->leftJoin($this->imageTable, $this->imageTable.'.id', '=', $this->userTenantSettingsTable.'.logo_id')
            ->first([
                $this->userTenantSettingsTable.".id",
                $this->userTenantSettingsTable.".creator",
                $this->userTenantSettingsTable.".author",
                $this->userTenantSettingsTable.".title",
                $this->userTenantSettingsTable.".subject",
                $this->userTenantSettingsTable.".keywords",
                $this->userTenantSettingsTable.".date_format",
                $this->userTenantSettingsTable.".money_format",
                $this->userTenantSettingsTable.".fee",
                $this->userTenantSettingsTable.".filename",
                $this->userTenantSettingsTable.".currency_id",
                $this->userTenantSettingsTable.".font_id",
                $this->userTenantSettingsTable.".bill_settings",
                $this->userTenantSettingsTable.".email",
                $this->userTenantSettingsTable.".phone",
                $this->userTenantSettingsTable.".web",
                $this->userTenantSettingsTable.".postcode",
                $this->userTenantSettingsTable.".city",
                $this->userTenantSettingsTable.".street",
                $this->imageTable.".url as logo",
            ]);
    }

    /**
     * @param array $attributes
     * @param int   $tenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCompanySettings(array $attributes, int $tenantId)
    {
        $model = $this->getCompanySettingsByTenantId($tenantId);

        if ($model) {
            return $this->update($attributes, $model->id);
        }

        return $this->create($attributes);
    }
}
