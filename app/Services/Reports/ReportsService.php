<?php

namespace App\Services\Reports;

use App\Models\BillingStatus;
use App\Services\BaseService;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class ReportsService
 *
 * @package App\Services\Reports
 */
class ReportsService extends BaseService
{
    /**
     * @param      $requestQuery
     * @param bool $canReadReportsFully
     * @param      $customTimerange
     *
     * @return array
     */
    public function filterReports($requestQuery, bool $canReadReportsFully, $customTimerange): array
    {
        $userTenant     = Auth::user()->getChosenTenant();
        $reportsConf    = config('reports');
        $reportsCast    = $reportsConf['casts'];

        $selectGroups   = !empty($requestQuery['selectGroups'])  ? $requestQuery['selectGroups']  : false;
        $selectBoards   = !empty($requestQuery['selectBoards'])  ? $requestQuery['selectBoards']  : false;
        $selectMembers  = !empty($requestQuery['selectMembers']) ? $requestQuery['selectMembers'] : false;
        $selectClients  = !empty($requestQuery['selectClients']) ? $requestQuery['selectClients'] : false;

        $pauses         = null;
        $timerBillingsIds = null;
        $timerange      = null;
        $grouping       = null;
        $details        = null;

        $record_id      = 'timer_id';
        $time_used      = 'time_used';
        $time_bill      = 'time_bill';
        foreach ($requestQuery as $selectedKey => $selectedValue) {
            if (isset($reportsCast[$selectedKey])) {
                switch ($reportsCast[$selectedKey]) {
                    case 'timerange':
                        $timerange = $this->handleSelectedPick($selectedValue, $reportsConf['options']['timerange']);
                        break;
                    case 'show':
                        break;
                    case 'grouping':
                        $grouping = $this->handleSelectedPick($selectedValue,  $reportsConf['options']['grouping']);
                        break;
                    case 'details':
                        $details = $this->handleSelectedPick($selectedValue, $reportsConf['options']['details']);
                        break;
                }
            }
        }

        $values = $this->getFilteredReports(
            $canReadReportsFully, $userTenant->id, $userTenant->tenant_id,
            $details, $selectGroups, $selectBoards, $selectMembers, $selectClients
        );

        $values = collect($values)->unique('log_id')->toArray();
        $pauses = $this->getDetailsPauses($canReadReportsFully, $userTenant);
        $reportsClass = $this->getGroupReportsDetailClass();

        if (isset($details['Summary'])){
            $reportsClass = $this->getGroupReportsSummaryClass();
        }

        if (is_array($selectClients) && count($selectClients) > 0){
            $timerBillingsIds = $this->getTimerBillingsIdsOfCustomer($selectClients);
            $timerBillingsIds = $timerBillingsIds->map(function ($item){
                return $item->id;
            });
        }

        $dateGroup = $this->initGroupReports($reportsClass, $grouping, $time_used, $time_bill);

        if ($timerBillingsIds == null || $timerBillingsIds->count() > 0) {
            $this->calculateTimerangeAndGrouping(
                $values, $dateGroup, $timerange, $pauses, $record_id, $time_used, $time_bill, $customTimerange, $timerBillingsIds
            );
        }

        return [
            'records'           => $dateGroup->getRecords(),
            'totalTimeUsed'     => $dateGroup->getTotalTimeUsed(),
            'totalTimeBill'     => $dateGroup->getTotalTimeBill(),
            'totalUnbilledTime' => $dateGroup->getTotalUnbilledTime(),
            'totalBilledTime'   => $dateGroup->getTotalBilledTime()
        ];
    }

    /**
     * @param              $values
     * @param GroupReports $dateGroup
     * @param              $timerange
     * @param              $pauses
     * @param              $record_id
     * @param              $time_used
     * @param              $time_bill
     * @param              $customTimerange
     * @param null         $timerBillingsIds
     */
    public function calculateTimerangeAndGrouping(
        &$values, GroupReports &$dateGroup, $timerange, $pauses, $record_id, $time_used, $time_bill, $customTimerange, $timerBillingsIds = null
    ) {
        $today      = $timerange['Today']           ?? null;
        $yesterday  = $timerange['Yesterday']       ?? null;
        $thisMonth  = $timerange['This Month']      ?? null;
        $lastMonth  = $timerange['Last Month']      ?? null;
        $thisWeek   = $timerange['This Week']       ?? null;
        $lastWeek   = $timerange['Last Week']       ?? null;
        $lastDays   = $timerange['Last 14 Days']    ?? null;
        $custom     = $timerange['Custom']          ?? null;

        $last14Days = Carbon::now()->subDays(14);
        $weekStart  = Carbon::now()->startOfWeek();
        $weekEnd    = Carbon::now()->endOfWeek();

        $customTimerangeStart   = isset($customTimerange[0]) ? Carbon::parse($customTimerange[0])->startOfDay() : null;
        $customTimerangeEnd     = isset($customTimerange[1]) ? Carbon::parse($customTimerange[1])->endOfDay() : null;

        $billTimerIds = [];

        foreach ($values as $value) {
            if (in_array($value->timer_billing_id, $billTimerIds)) {
                continue;
            }

            $billTimerIds[] = $value->timer_billing_id;
            $totalDurationTimeUsed = TimeService::diffTimes($value->start_time, $value->end_time);

            if ($totalDurationTimeUsed < 60) {
                continue;
            }

            $value->time_used = TimeService::toCarbonInterval($totalDurationTimeUsed);

            if (is_string($value->time_bill)) {
                $value->time_bill = TimeService::stringToCarbonInterval($value->time_bill);
            }

            if (is_string($value->bill_timer)) {
                $value->bill_timer = TimeService::stringToCarbonInterval($value->bill_timer);
            }

            if ($pauses && isset($pauses[$value->{$record_id}])) {
                $pauseSeconds   = (int) $pauses[$value->{$record_id}];

                $pauseInterval  = TimeService::toCarbonInterval($pauseSeconds);
                $pauseCarbon    = Carbon::now()->add($pauseInterval);
                $timeCarbon     = Carbon::now()->add($value->time_used);

                $totalDuration = $pauseCarbon->diffInSeconds($timeCarbon);

                if ($totalDuration < 60) {
                    continue;
                }

                $value->time_used = TimeService::toCarbonInterval($totalDuration);
            }

            /** @var $timerBillingsIds \Illuminate\Support\Collection|null */
            if (BillingStatus::INITIAL_STATUSES['Billed']['id'] === $value->billing_status_id) {
                $value->billed_time = $value->bill_timer ?? $value->time_bill;

                if ($timerBillingsIds && $timerBillingsIds->isNotEmpty()) {
                    if (!$timerBillingsIds->contains($value->timer_billing_id)) {
                        continue;
                    }
                }
            }else{
                $value->billed_time = CarbonInterval::seconds(0);

                if ($timerBillingsIds && $timerBillingsIds->isNotEmpty()) {
                    continue;
                }
            }

            $value->unbilled_time_total = TimeService::diffCarbonIntervals($value->time_bill, $value->billed_time);

            if ($value->end_time && $timerange) {
                $createdAt = TimeService::toUserLocalTime(Carbon::parse($value->end_time));

                if ($today) {
                    if ($createdAt->isToday()) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($yesterday) {
                    if ($createdAt->isYesterday()) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($thisMonth) {
                    if ($createdAt->isCurrentYear() && $createdAt->isCurrentMonth()) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($lastWeek) {
                    if (($createdAt->weekOfYear === 36 || $createdAt->isCurrentYear()) && $createdAt->isLastWeek()) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($custom && $customTimerangeStart && $customTimerangeEnd) {
                    if (TimeService::dateBetween($createdAt, $customTimerangeStart, $customTimerangeEnd)) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($thisWeek) {
                    if ($createdAt->between($weekStart, $weekEnd)) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($lastDays) {
                    if ($createdAt->greaterThan($last14Days)) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }

                if ($lastMonth) {
                    $lastMonth = Carbon::now()->subMonth()->startOfMonth();
                    if ($createdAt->greaterThan($lastMonth->startOfMonth()) && $createdAt->lessThan($lastMonth->endOfMonth())) {
                        $dateGroup->addRecord($value);
                        continue;
                    }
                }
            } else {
//                $dateGroup->addRecord($value);
            }
        }

        $this->calculateTotalRecord($dateGroup);
    }

    /**
     * @param GroupReports $dateGroup
     */
    public function calculateTotalRecord(GroupReports &$dateGroup) : void
    {
        foreach ($dateGroup->getRecords() as $record) {
            if (is_array($record)) {
                foreach ($record as $item) {
                    $dateGroup->calcRecordTotal($item);
                }
            } else {
                $dateGroup->calcRecordTotal($record);
            }
        }
    }

    /**
     * @param string $groupReports
     * @param        $grouping
     * @param string $time_used
     * @param string $time_bill
     *
     * @return GroupReports
     */
    protected function initGroupReports(string $groupReports, $grouping, string $time_used, string $time_bill): GroupReports
    {
        $dateGroup = new $groupReports("None", $time_used, $time_bill);

        if ($grouping) {
            foreach ($grouping as $groupBy => $group) {
                $dateGroup = new $groupReports($groupBy, $time_used, $time_bill);
                break;
            }
        }

        return $dateGroup;
    }

    /**
     * @return string
     */
    protected function getGroupReportsDetailClass(): string
    {
        return GroupReportsDetail::class;
    }

    /**
     * @return string
     */
    protected function getGroupReportsSummaryClass(): string
    {
        return GroupReportsSummary::class;
    }

    /**
     * @param $canReadReportsFully
     * @param $userTenant
     *
     * @return array
     */
    public function getDetailsPauses($canReadReportsFully, $userTenant)
    {
        $pauses = app('PauseRepo')->getDetailsPauses($canReadReportsFully, $userTenant);

        return $this->computePauses($pauses);
    }

    /**
     * @param $canReadReportsFully
     * @param $userTenant
     *
     * @return array
     */
    public function getSummaryPauses($canReadReportsFully, $userTenant)
    {
        $pauses = app('PauseRepo')->getSummaryPauses($canReadReportsFully, $userTenant);

        return $this->computePauses($pauses);
    }

    /**
     * @param $pauses
     *
     * @return array
     */
    private function computePauses($pauses)
    {
        $pausesComputed = [];

        if ($pauses) {
            foreach ($pauses as $pause) {
                $pausesComputed [$pause->id] = $pause->total_pause;
            }
        }

        return $pausesComputed;
    }

    /**
     * @param array $selectedClients
     *
     * @return Collection
     */
    public function getTimerBillingsIdsOfCustomer(array $selectedClients): Collection
    {
        return app('TimerBillingRepo')->getTimerBillingsIdsOfCustomer($selectedClients);
    }

    /**
     * @param array $selectedValue
     * @param array $configRecords
     *
     * @return array|null
     */
    public function handleSelectedPick(array $selectedValue, array $configRecords): ?array
    {
        $configRecords = array_column($configRecords, 'name', 'id');

        sort($selectedValue);

        $resultRecords = null;

        foreach ($selectedValue as $selected){
            $resultRecords[$selected] = $configRecords[$selected];
        }

        return is_array($resultRecords)? array_flip($resultRecords) : null;
    }

    /**
     * @param bool  $canReadReportsFully
     * @param       $userTenantId
     * @param       $tenantId
     * @param array $details
     * @param bool  $selectGroups
     * @param bool  $selectBoards
     * @param bool  $selectMembers
     * @param bool  $selectClients
     *
     * @return array|null
     */
    public function getFilteredReports(
        bool $canReadReportsFully, $userTenantId, $tenantId, $details = [], $selectGroups = false, $selectBoards = false, $selectMembers = false, $selectClients = false
    ) {
        $timerService = app('TimerSer');

        if ($userTenantId && $tenantId) {
            return $timerService->getReportsTimers($canReadReportsFully, $userTenantId, $tenantId, $selectGroups, $selectBoards, $selectMembers, $selectClients);
        }
    }

    /**
     * @param $time
     *
     * @return string
     */
    public function formatDate($time) : string
    {
        $time = CarbonInterval::instance($time);

        $days = $time->dayz > 0 ? $time->dayz . 'd ' : '';
        $hours = $time->hours > 0 ? $time->hours . ' h ' : '';
        $minutes = $time->minutes > 0 ? $time->minutes . ' m' : '';

        return $days . $hours . $minutes;
    }
}
