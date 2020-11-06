<?php

namespace App\Services\Billing;

use App\Models\BillingStatus;
use App\Models\Timer;
use App\Models\TimerBilling;
use App\Repositories\TimerRepositoryEloquent;
use App\Services\Reports\ReportsService;
use App\Services\Time\TimeService;
use Carbon\Carbon;

class BillingService
{
    /** @var ReportsService */
    private $reportsService;

    /** @var TimerRepositoryEloquent */
    private $timerRepo;

    /**
     * BillingService constructor.
     */
    public function __construct()
    {
        $this->reportsService   = app('ReportsSer');
        $this->timerRepo        = app('TimerRepo');
    }

    /**
     * @param Timer $timer
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Exception
     */
    public function createTimerBill(Timer $timer)
    {
        return app('TimerBillingRepo')->create([
            'timer_id'          => $timer->id,
            'billing_status_id' => BillingStatus::INITIAL_STATUSES['Open']['id'],
            'time_bill'         => TimeService::getRoundTrackedTimeByTimer($timer)
        ]);
    }

    /**
     * @param Timer $timer
     *
     * @return bool
     * @throws \Exception
     */
    public function updateTimerBill(Timer $timer)
    {
        return $timer->billing->update([
            'time_bill' => TimeService::getRoundTrackedTimeByTimer($timer)
        ]);
    }

    /**
     * @param int|Timer $timer
     *
     * @return bool
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function updateOrCreateTimerBilling($timer)
    {
        if (is_integer($timer)) {
            $timer = app('TimerRepo')->find($timer);
        }

        if (is_null($timer->end_time) || is_null($timer->task_id)) {
            return false;
        }

        if ($timer->billing) {
            return (bool) $this->updateTimerBill($timer);
        }

        return (bool) $this->createTimerBill($timer);
    }









    /**
     * @param          $userTenant
     * @param int|null $year
     *
     * @return array
     */
    public function getBillingYearOverview($userTenant, int $year = null)
    {
        $time_used  = 'time_used';
        $time_bill  = 'bill_timer';
        $time_range = ['Custom' => true];
        $carbonDate = Carbon::now();

        if ($year) {
            $carbonDate->year = $year;
        }

        $customTimeRange = [
            $carbonDate->copy()->startOfYear(),
            $carbonDate->copy()->endOfYear()
        ];

        $values = $this->timerRepo->getReportsTimers(true, $userTenant->id, $userTenant->tenant_id);
        $pauses = $this->reportsService->getDetailsPauses(true, $userTenant);
        $dateGroup = new GroupYearOverviewByBoard('By Month', $time_used, $time_bill);

        $values = collect($values)->unique('log_id')->toArray();
        $pauses = collect($pauses)->unique('id')->toArray();
        
        $this->reportsService->calculateTimerangeAndGrouping(
            $values, $dateGroup, $time_range, $pauses, 'timer_id', $time_used, $time_bill, $customTimeRange
        );

        return $dateGroup->getRecords();
    }

    /**
     * @param int $timerBillingId
     * @param int $billingStatusId
     *
     * @return TimerBilling
     * @throws \Exception
     */
    public function changeTimerBillingStatus(int $timerBillingId, int $billingStatusId ): TimerBilling
    {
        $timerBilling = app('TimerBillingRepo')->find($timerBillingId);

        if ($timerBilling->billing_status_id === BillingStatus::INITIAL_STATUSES['Billed']['id'] || $billingStatusId === BillingStatus::INITIAL_STATUSES['Billed']['id']) {
            throw new \Exception('You can\'t change billed timer or assign status billed manually');
        }

        $timerBilling->billing_status_id = $billingStatusId;
        $timerBilling->save();

        return $timerBilling;
    }

    /**
     * @param array $timerBillingIds
     * @param int   $billingStatusId
     *
     * @return mixed
     */
    public function massChangeTimerBillingStatus(array $timerBillingIds, int $billingStatusId )
    {
        return app('TimerBillingRepo')->massUpdate($timerBillingIds, $billingStatusId);
    }



}
