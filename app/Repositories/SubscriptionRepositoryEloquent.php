<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 26.06.17
 * Time: 17:13
 */

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\Tenant;

class SubscriptionRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Subscription::class;
    }

    /**
     * @param Tenant $tenant
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createTenantSubscription(Tenant $tenant)
    {
        return $this->create([
            'active_to' => $tenant->created_at->addMonth()->toDateTimeString(),
            'tenant_id' => $tenant->id
        ]);
    }
}
