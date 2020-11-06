<?php

namespace App\Services\Bill;

use App\Models\Bill;
use App\Models\BillingStatus;
use App\Models\BillLog;
use App\Models\BillTimer;
use App\Models\TimerBilling;
use App\Repositories\BillRepositoryEloquent;
use App\Repositories\BillTimerRepositoryEloquent;
use App\Services\Reports\ReportsService;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillService extends ReportsService
{
    /** @var BillRepositoryEloquent */
    private $billRepo;

    /** @var BillTimerRepositoryEloquent */
    private $BillTimerRepo;

    /**
     * BillService constructor.
     */
    public function __construct()
    {
        $this->billRepo         = app('BillRepo');
        $this->BillTimerRepo    = app('BillTimerRepo');
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public static function dateToFormat(string $date, string $format = 'd.m.Y')
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * @param array $attributes
     * @param null  $billId
     * @param bool  $save
     *
     * @return Bill|\Exception|mixed
     */
    public function createDraftBill(array $attributes, $billId = null, bool $save = false)
    {
        $total_time   = CarbonInterval::create(0);
        $total_amount = 0;

        try{
            DB::beginTransaction();

            /** @var $bill Bill */
            $bill = $this->billRepo->createOrUpdate($attributes, $billId);

            foreach ($attributes['bill_timers'] as $billTimer) {
                if ($billTimer['timerBillingStatusId'] === BillingStatus::INITIAL_STATUSES['Billed']['id']) {
                    continue;
                }

                /** @var TimerBilling $timerBilling */
                $timerBilling = app('TimerBillingRepo')->with(['timer', 'timer.userTenant'])->find($billTimer['timerBillingId']);

                if (!$timerBilling) {
                    throw new \Exception('TimerBilling not found');
                }

                /** @var BillTimer $billTimer */
                $billTimer = app('BillTimerRepo')->createOrUpdateBillTimer([
                    'bill_id'           => $bill->id,
                    'user_id'           => $timerBilling->timer->userTenant->user_id,
                    'comment'           => $timerBilling->timer->comment,
                    'time'              => $timerBilling->time_bill,
                    'timer_billing_id'  => $timerBilling->id,
                    'rate'              => $attributes['customer_rate'],
                ]);

                $time = TimeService::stringToCarbonInterval($billTimer->time);

                $total_time->add($time);
                $total_amount += $time->totalHours * $billTimer->rate;
            }

            $bill->customer_id  = $attributes['customer_id'];
            $bill->rate         = $attributes['customer_rate'];
            $bill->time         = TimeService::getTotalHours($total_time);
            $bill->amount       = self::roundFloatUp($total_amount);
            $bill->save();
            
            $bill->logs()->create([
                'user_id' => Auth::id(),
                'action' => BillLog::ACTION_CREATE
            ]);

            $this->billRepo->createOrUpdateBillLayout([
                'bill_layout_type_id'   => $attributes['bill_layout_type_id'],
            ], $bill->id);

            if ($save) {
                DB::commit();
            }

            return $bill;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    /**
     * @param array $attributes
     * @param null  $billId
     *
     * @return Bill|\Exception
     */
    public function createOrUpdateBill(array $attributes, $billId = null)
    {
        $total_time = CarbonInterval::create(0);
        $total_amount = 0;

        try {
            DB::beginTransaction();

            /** @var $bill Bill */
            $bill = $this->billRepo->createOrUpdate($attributes, $billId);

            // Deleting deleted bill timers
            $billTimersIds  = array_column($attributes['bill_timers'], 'billTimerId');
            $billTimers     = $bill->billTimers->whereNotIn('id', $billTimersIds);

            foreach ($billTimers as $billTimer) {
                app('BillTimerRepo')->deleteBillTimer($billTimer->id);
            }

            foreach ($attributes['bill_timers'] as $billTimer) {
                app('BillTimerRepo')->createOrUpdateBillTimer([
                    'bill_id'           => $bill->id,
                    'title'             => !empty($billTimer['billTitle']) ? $billTimer['billTitle'] : '',
                    'comment'           => $billTimer['billTimerComment'],
                    'rate'              => $billTimer['billTimerRate'],
                    'unit'              => $billTimer['billUnit'] ?? '',
                    'time'              => $billTimer['billTimerTime'],
                    'user_id'           => $billTimer['billTimerUserId'],
                    'timer_billing_id'  => $billTimer['timerBillingId'],
                ], $billTimer['billTimerId']);

                $time = TimeService::stringToCarbonInterval($billTimer['billTimerTime']);

                $total_time->add($time);
                $total_amount += (float) $billTimer['billTimerAmount'];
            }

            $bill->customer_id      = $attributes['customer_id'];
            $bill->tenant_id        = $attributes['tenant_id'];
            $bill->rate             = $attributes['customer_rate'];
            $bill->status           = $attributes['status'];
            $bill->invoice_number   = $attributes['invoice_number'];
            $bill->time             = TimeService::getTotalHours($total_time);
            $bill->amount           = self::roundFloatUp($total_amount);
            $bill->save();
            
            $bill->logs()->create([
                'action' => BillLog::ACTION_UPDATE,
                'comment' => $attributes['bill_log_comment'],
                'user_id' => Auth::id()
            ]);

            $this->billRepo->createOrUpdateBillLayout([
                'bill_layout_type_id'   => $attributes['bill_layout_type_id'],
                'bill_date'             => $attributes['bill_date']
            ], $bill->id);

            DB::commit();

            return $bill;
        }catch (\Exception $e){
            DB::rollBack();
            return $e;
        }
    }

    /**
     * @param int $billId
     * @return mixed
     */
    public function getBillWithRelations(int $billId) :?Bill
    {
        return app('BillRepo')->with([
            'billTimers' => function($query) {
                $query->with(['user']);
            },
            'customer',
            'billLayout',
            'billLayout.billLayoutType',
            'timerBillings.timer',
            'timerBillings.timer.userTenant',
            'timerBillings.timer.userTenant.user',
            'timerBillings.timer.task',
            'timerBillings.timer.task.board',
            'timerBillings.timer.task.board.group',
            'logs'
        ])->find($billId);
    }

    /**
     * @param $requestQuery
     *
     * @return array
     */
    public function getFilteredBills($requestQuery): array
    {
        $canReadReportsFully = true;
        $billConf           = config('bill');
        $billCast           = $billConf['casts'];
        $selectBoards       = $requestQuery['selectBoards'] ?? false;
        $selectMembers      = $requestQuery['selectMembers'] ?? false;
        $customTimerange    = $requestQuery['customTimerange'] ?? null;
        $computedSelected   = $this->computeSelectedInToArrayNames($requestQuery, $billCast, $billConf);
        $userTenant         = Auth::user()->getChosenTenant();

        if ($selectMembers[0] == 0){
            $selectMembers = false;
        }

        $values = $this->getFilteredReports(
            $canReadReportsFully, $userTenant->id, $userTenant->tenant_id, null, null, $selectBoards, $selectMembers, null
        );

        $values = collect($values)->unique('log_id')->toArray();

        $pauses     = null;
        $record_id  = 'timer_id';
        $time_used  = 'time_used';
        $time_bill  = 'time_bill';

        $pauses     = $this->getDetailsPauses($canReadReportsFully, $userTenant);
        $groupBill  = $this->initGroupBills($time_used, $time_bill);
        $billFilter = $this->initBillFilter($computedSelected['billStatusFilter'], $computedSelected['timeFilter'], $time_bill);

        $groupBill->setFilter($billFilter);

        $this->calculateTimerangeAndGrouping(
            $values, $groupBill, $computedSelected['period'], $pauses, $record_id, $time_used, $time_bill, $customTimerange
        );
        return [
            'records'           => $groupBill->getRecords(),
            'totalTimeUsed'     => $groupBill->getTotalTimeUsed(),
            'totalTimeBill'     => $groupBill->getTotalTimeBill(),
            'totalUnbilledTime' => $groupBill->getTotalUnbilledTime(),
            'totalBilledTime'   => $groupBill->getTotalBilledTime()
        ];
    }

    /**
     * @param       $requestQuery
     * @param array $billCast
     * @param array $billConf
     *
     * @return array
     */
    public function computeSelectedInToArrayNames($requestQuery, array $billCast, array $billConf): array
    {
        $computedNameArray = [
            'period'            => null,
            'billStatusFilter'  => null,
            'timeFilter'        => null
        ];

        foreach ($requestQuery as $selectedKey => $selectedValue) {
            if (isset($billCast[$selectedKey])) {
                switch ($billCast[$selectedKey]) {
                    case 'period':
                        $computedNameArray['period'] = $this->handleSelectedPick(
                            $selectedValue,
                            $billConf['options']['period']);
                        break;
                    case 'bill':
                        $computedNameArray['billStatusFilter'] = $this->handleSelectedPick(
                            $selectedValue,
                            $billConf['options']['bill']);
                        break;
                    case 'time_filter':
                        $computedNameArray['timeFilter'] = $this->handleSelectedPick(
                            $selectedValue,
                            $billConf['options']['time_filter']);
                        break;
                }
            }
        }

        return $computedNameArray;
    }

    /**
     * @param $time_used
     * @param $time_bill
     *
     * @return GroupBill
     */
    public function initGroupBills($time_used, $time_bill): GroupBill
    {
        return new GroupBill("None", $time_used, $time_bill);
    }

    /**
     * @param $billStatusFilter
     * @param $timeFilter
     * @param $time_bill
     *
     * @return BillFilter
     */
    public function initBillFilter($billStatusFilter, $timeFilter, $time_bill): BillFilter
    {
        $billStatusFilter = (is_array($billStatusFilter) && count($billStatusFilter) > 0) ? array_keys($billStatusFilter)[0] : 'None';
        $timeFilter = (is_array($timeFilter) && count($timeFilter) > 0) ? array_keys($timeFilter)[0] : 'None';

        return new BillFilter($billStatusFilter, $timeFilter, $time_bill);
    }

    /**
     * @param CarbonInterval $time
     *
     * @return string
     */
    public function parseCarbonInterval(CarbonInterval $time): string
    {
        return $this->formatDate($time);
    }

    /**
     * @param $timerBilling
     * @param $billTimer
     * @param $sum
     *
     * @throws \Exception
     */
    public function makeBillTimer(&$timerBilling, &$billTimer, &$sum)
    {
        $computed_time = TimeService::getTotalHours(TimeService::stringToCarbonInterval($billTimer->time));

        $timerBilling['bill_timer'] = $billTimer;
        $timerBilling['bill_timer']['computed_time'] = $computed_time;
        $timerBilling['bill_timer']['sum'] = round($computed_time * $billTimer->rate, 2);

        $sum += $timerBilling['bill_timer']['sum'];
    }

    /**
     * @param int $tenantId
     * @param array $month
     *
     * @param int $customerId
     * @return Collection
     */
    public function getBillListData(int $tenantId, array $month, int $customerId = null) :Collection
    {
        $query = Bill::query();

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $query->where('tenant_id', $tenantId)
            ->with('billLayout');

        if ($month) {
            $query->whereHas('billLayout', function ($query) use ($month) {
                /** @var Builder $query */
                if ($month) {
                    $query->whereBetween('bill_date', $month);
                }
            });
        }

        return $query->get();
    }

    /**
     * @return mixed
     */
    public function getAddBill() :Collection
    {
        return app('BillRepo')->all();
    }

    /**
     * @param int $billId
     * @return \Illuminate\Support\Collection
     */
    public function getLogs($billId) : \Illuminate\Support\Collection
    {
        return app('BillRepo')
            ->with('logs')
            ->findWhere(['id' => $billId])
            ->toBase();
    }

    /**
     * @param $billId
     *
     * @throws \Throwable
     */
    public function deleteBill($billId)
    {
        // Soft Delete strategy
        DB::transaction(function () use ($billId) {
            /** @var $bill Bill */
            $bill = app('BillRepo')->with(['billTimers', 'timerBillings'])->find($billId);

            foreach ($bill->billTimers as $billTimer) {
                $billTimer->delete();
            }

            foreach ($bill->timerBillings as $timerBilling) {
                $timerBilling->billing_status_id = BillingStatus::INITIAL_STATUSES['Open']['id'];
                $timerBilling->save();
            }
            
            $bill->logs()->create([
                'action' => BillLog::ACTION_DELETE,
                'user_id' => Auth::id()
            ]);
            
            return $bill->delete();
        }, 2);
    }

    public function getNextInvoiceNumber(int $tenantId): int
    {
        $lastBill = Bill::withTrashed()
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastBill ? $lastBill->invoice_number + 1 : 1;
    }
}
