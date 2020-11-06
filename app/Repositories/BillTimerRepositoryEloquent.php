<?php

namespace App\Repositories;

use App\Models\BillingStatus;
use App\Models\BillTimer;

/**
 * Class BillTimerRepositoryEloquent
 * @package App\Repositories
 * @property BillTimer $model
 */
class BillTimerRepositoryEloquent extends BaseRepositoryEloquent
{
    public function model()
    {
        return BillTimer::class;
    }

    /**
     * @param array $attributes
     * @param null  $billTimerId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrUpdateBillTimer(array $attributes, $billTimerId = null)
    {
        $billTimer = $this->updateOrCreate(['id'=> $billTimerId], array_merge(['id'=> $billTimerId], $attributes));

        if (!isset($attributes['billing_status_id'])) {
            $attributes['billing_status_id'] = BillingStatus::INITIAL_STATUSES['Billed']['id'];
        }

        if ($billTimer->timerBilling) {
            $billTimer->timerBilling()->update([
                'billing_status_id' => $attributes['billing_status_id']
            ]);
        }

        return $billTimer;
    }

    /**
     * @param null $billTimerId
     *
     * @return mixed
     */
    public function deleteBillTimer($billTimerId = null)
    {
        $billTimer = $this->find($billTimerId);

        if ($billTimer->timerBilling) {
            $billTimer->timerBilling()->update([
                'billing_status_id' => BillingStatus::INITIAL_STATUSES['Open']['id']
            ]);
        }

        return $billTimer->delete();
    }
}
