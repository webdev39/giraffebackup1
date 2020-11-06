<?php

namespace App\Services\Filter;

use App\Repositories\FilterRepositoryEloquent;
use App\Services\BaseService;

/**
 * Class FilterService
 *
 * @package App\Services\Filter
 */
class FilterService extends BaseService
{
    /** @var FilterRepositoryEloquent */
    private $filterRepo;

    /**
     * FilterService constructor.
     */
    public function __construct()
    {
        $this->filterRepo = app('FilterRepo');
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getFiltersByUserTenantId(int $userTenantId)
    {
        return $this->filterRepo->findWhere(['user_tenant_id' => $userTenantId]);
    }

    /**
     * @param int $filterId
     *
     * @return mixed
     */
    public function getFilterById(int $filterId)
    {
        return $this->filterRepo->find($filterId);
    }

    /**
     * @param int $filterId
     *
     * @return int
     */
    public function remove(int $filterId)
    {
        return $this->filterRepo->delete($filterId);
    }

    /**
     * @param array $attributes
     * @param int   $userTenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes, int $userTenantId)
    {
        $attributes['user_tenant_id'] = $userTenantId;

        return $this->filterRepo->create($attributes);
    }

    /**
     * @param array $attributes
     * @param int   $filterId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, int $filterId)
    {
        return $this->filterRepo->update($attributes, $filterId);
    }

    /**
     * @param int $filterId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTaskByFilterId(int $filterId)
    {
        return $this->filterRepo->findTaskIdsByFilterId($filterId);
    }
}