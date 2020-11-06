<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\UserTenant;
use Illuminate\Support\Collection;

/**
 * Class CustomerRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property Customer $model
 */
class CustomerRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Customer::class;
    }

    /**
     * @param int $tenantId
     * @param int $customId
     *
     * @return mixed
     */
    public function getCustomerByCustomId(int $tenantId, int $customId)
    {
        return $this->findWhere(['tenant_id' => $tenantId, 'custom_id' => $customId])->first();
    }

    /**
     * @param int $customerId
     *
     * @return mixed
     */
    public function getCustomerById(int $customerId)
    {
        return $this->find($customerId);
    }

    /**
     * @param UserTenant  $userTenant
     * @param string|null $status
     *
     * @return Collection
     */
    public function getCustomersByUserTenant(UserTenant $userTenant, string $status = null) : Collection
    {
        $query = $this->model->where('tenant_id', $userTenant->tenant_id);

        if ($status) {
            $query = $query->status($status);
        }

        return $query->get();
    }

    /**
     * @param array $attributes
     * @param int   $tenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createCustomer(array $attributes, int $tenantId)
    {
        return $this->create(array_merge($attributes, [
            'status'    => 'active',
            'tenant_id' => $tenantId,
        ]));
    }

    /**
     * @param int $customerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function deleteCustomer(int $customerId)
    {
        return $this->delete($customerId);
    }
}
