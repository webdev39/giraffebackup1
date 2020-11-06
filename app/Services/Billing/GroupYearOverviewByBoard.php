<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 12.04.18
 * Time: 11:44
 */

namespace App\Services\Billing;

use App\Models\BillingStatus;
use App\Services\Reports\GroupReportsSummary;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use stdClass;

/**
 * Class GroupYearOverviewByBoard
 * @package App\Services\Billing
 */
class GroupYearOverviewByBoard extends GroupReportsSummary
{
    /**
     * @var array
     */
    private $recordProperties = [
        'board_id',
        'board_name',
        'group_id',
        'group_name',
        'total_time_used',
        'total_time_bill',
        'total_billed_time',
        'total_unbilled_time',
        'unbilled_time',
        'not_billable',
        'parked_time',
        'billed_time',
        'all_time',
        'created_at',
    ];

    /**
     * @var array
     */
    private $billStatuses = [];

    /**
     * GroupYearOverviewByBoard constructor.
     *
     * @param $groupBy
     * @param $time_used
     * @param $time_bill
     */
    public function __construct($groupBy, $time_used, $time_bill)
    {
        parent::__construct($groupBy, $time_used, $time_bill);

        for ($m = 1; $m <= 12; ++$m) {
            $this->records[date('F', mktime(0, 0, 0, $m, 1))] = null;
        }

        foreach (BillingStatus::INITIAL_STATUSES as $billing_statuses) {
            if ($billing_statuses['id'] === BillingStatus::INITIAL_STATUSES['Unknown']['id']){
                $this->billStatuses[$billing_statuses['id']] = snake_case($billing_statuses['name']);
                continue;
            }

            $this->billStatuses[$billing_statuses['id']] = snake_case($billing_statuses['name']) . '_time';
        }

        $this->billStatuses[5] = 'all_time';
    }

    /**
     * @param $record
     *
     * @throws \Exception
     */
    protected function groupByDay($record)
    {
       throw new \Exception('This Method is not Available');
    }

    /**
     * @param $record
     *
     * @throws \Exception
     */
    protected function groupByWeek($record)
    {
        throw new \Exception('This Method is not Available');
    }

    /**
     * @param $record
     */
    protected function groupByMonth($record)
    {
        $month = self::getCreateAt($record)->format('F');

        $this->addSummaryRecord($this->records[$month][$record->board_id], $record);
    }

    /**
     * @param $record
     *
     * @throws \Exception
     */
    protected function groupByYear($record)
    {
        throw new \Exception('This Method is not Available');
    }

    /**
     * @param $record
     */
    protected function noneGroupBy($record)
    {
        $this->addSummaryRecord($this->records[(int)$record->board_id], $record);
    }

    /**
     * @return bool
     */
    protected function sortRecords()
    {
        foreach ($this->records as $key => $record){
            if (is_array($record)){
                $this->records[$key] = array_values($record);
            }
        }

        return true;
    }

    /**
     * @param $recordValue
     * @param $record
     */
    private function addSummaryRecord(&$recordValue, $record)
    {
        if (!is_null($recordValue)) {
            $this->addTimeToRecord($recordValue, $record);
        } else {
            $recordValue = $this->createRecord($record);
        }
    }

    private function addTimeToRecord(&$recordValue, $record)
    {
        $recordValue->total_time_used->add($record->time_used);
        $recordValue->total_time_bill->add($record->time_bill);
        $recordValue->total_billed_time->add($record->billed_time ?? $record->bill_timer);

        if (isset($record->unbilled_time_total)) {
            $recordValue->total_unbilled_time->add($record->unbilled_time_total);
        }

        foreach ($this->billStatuses as $billingStatusId => $billingStatusName) {
            if ($record->billing_status_id === $billingStatusId) {
                $recordValue->{$billingStatusName}->add($record->time_bill);
            }
        }

        $recordValue->all_time = $recordValue->total_billed_time;
        
        foreach ($recordValue as $key => $value) {
            if ($value instanceof CarbonInterval) {
                $recordValue->{$key} = TimeService::toCarbonInterval(0, $value->i, $value->h);
            }
        }
    }

    private function createRecord($record)
    {
        $newRecord = new stdClass;

        $record->total_time_used        = CarbonInterval::create(0);
        $record->total_time_bill        = CarbonInterval::create(0);
        $record->total_billed_time      = CarbonInterval::create(0);
        $record->total_unbilled_time    = CarbonInterval::create(0);

        $record->total_time_used->add($record->time_used);
        $record->total_time_bill->add($record->time_bill);
        $record->total_billed_time->add($record->billed_time ?? $record->bill_timer);

        if (isset($record->unbilled_time_total)) {
            $record->total_unbilled_time->add($record->unbilled_time_total);
        }

        foreach ($this->billStatuses as $billingStatusId => $billingStatusName) {
            $record->{$billingStatusName} = CarbonInterval::create(0);

            if ($record->billing_status_id === $billingStatusId){
                $record->{$billingStatusName} = CarbonInterval::create(0)->add($record->time_bill);
            }
        }

        $record->all_time = $record->total_billed_time;
        $objectVars = get_object_vars($record);

        foreach ($objectVars as $key => $value) {
            if (in_array($key, $this->recordProperties)) {
                if ($value instanceof CarbonInterval) {
                    $newRecord->{$key} = TimeService::toCarbonInterval(0, $value->i, $value->h);
                } else {
                    $newRecord->{$key} = $value;
                }
            }
        }

        return $newRecord;
    }
}