<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 09.03.18
 * Time: 11:01
 */

namespace App\Repositories;

use App\Models\TimerBilling;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimerBillingRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * @return string
     */
    public function model()
    {
        return TimerBilling::class;
    }

    /**
     * @param array $timerBillingIds
     * @param int $billingStatusId
     * @return mixed
     */
    public function massUpdate(array $timerBillingIds, int $billingStatusId)
    {
        return $this->model()::whereIn('id', $timerBillingIds)->update([
            'billing_status_id' => $billingStatusId
        ]);
    }

    /**
     * @param array $customersIds
     * @return Collection
     */
    public function getTimerBillingsIdsOfCustomer(array $customersIds): Collection
    {
        $timerBillingTable    = app('TimerBillingRepo')->model->getTable();
        $billTimerTable       = app('BillTimerRepo')->model->getTable();
        $billTable            = app('BillRepo')->model->getTable();
        $customerTable        = app('CustomerRepo')->model->getTable();

        return DB::table($timerBillingTable)
            ->select(
                $timerBillingTable . '.id'
            )
            ->join(
                $billTimerTable,
                $timerBillingTable . '.id',
                '=',
                $billTimerTable . '.timer_billing_id'
            )
            ->join(
                $billTable,
                $billTimerTable . '.bill_id',
                '=',
                $billTable . '.id'
            )
            ->join(
                $customerTable,
                $billTable . '.customer_id',
                '=',
                $customerTable . '.id'
            )
            ->whereNull($billTable . '.deleted_at')
            ->whereNull($billTimerTable . '.deleted_at')
            ->whereIn($customerTable . '.id', $customersIds)
            ->get();
    }

}