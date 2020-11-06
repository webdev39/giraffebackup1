<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\UserTenant;
use App\Repositories\CustomerRepositoryEloquent;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class CustomerService extends BaseService
{
    /** @var CustomerRepositoryEloquent */
    private $customerRepo;

    /**
     * CustomerService constructor.
     */
    public function __construct()
    {
        $this->customerRepo = app('CustomerRepo');
    }

    /**
     * @param int $customerId
     *
     * @return mixed
     */
    public function getCustomerById(int $customerId)
    {
        return $this->customerRepo->getCustomerById($customerId);
    }

    /**
     * @param UserTenant  $userTenant
     * @param string|null $status
     *
     * @return Collection
     */
    public function getCustomersByUserTenant(UserTenant $userTenant, string $status = null) : Collection
    {
        return $this->customerRepo->getCustomersByUserTenant($userTenant, $status);
    }

    /**
     * @param $tenantId
     * @param $customId
     *
     * @return mixed
     */
    public function getCustomerByCustomId($tenantId, $customId)
    {
        return $this->customerRepo->getCustomerByCustomId($tenantId, $customId)->first();
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
        return $this->customerRepo->createCustomer($attributes, $tenantId);
    }

    /**
     * @param array $attributes
     * @param int   $customerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCustomer(array $attributes, int $customerId)
    {
        return $this->customerRepo->update($attributes, $customerId);
    }

    /**
     * @param array $attributes
     * @param int   $customerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function archiveCustomer(int $customerId)
    {
        return $this->customerRepo->update(['status' => 'archived'], $customerId);
    }

    /**
     * @param array $attributes
     * @param int   $customerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function restoreCustomer(int $customerId)
    {
        return $this->customerRepo->update(['status' => 'active'], $customerId);
    }

    /**
     * @param $customerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function destroyCustomer($customerId)
    {
        return $this->customerRepo->deleteCustomer($customerId);
    }
}