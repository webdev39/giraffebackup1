<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 28.03.18
 * Time: 14:32
 */

namespace App\Services\Reports;


use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use stdClass;

/**
 * Class GroupReportsSummary
 * @package App\Services\Reports
 */
class GroupReportsSummary extends GroupReports
{
    /**
     * @var array
     */
    private $recordProperties = [
        'task_id','task_name','created_at','board_id','board_name','group_id', 'group_name',
        'billing_status_name','billing_status_alias', 'billing_status_color', 'users_name',
        'total_time_used','total_billed_time', 'billed_time', 'unbilled_time_total'];

    /**
     * GroupReportsSummary constructor.
     *
     * @param $groupBy
     * @param $time_used
     * @param $time_bill
     */
    public function __construct($groupBy, $time_used, $time_bill)
    {
        parent::__construct($groupBy, $time_used, $time_bill);
    }

    /**
     * @param $record
     */
    protected function groupByDay($record)
    {
        $day = self::getCreateAt($record)->toDateString();

        $this->addSummaryRecord($this->records[$day][$record->task_id], $record);
    }

    /**
     * @param $record
     */
    protected function groupByWeek($record)
    {
        $startOfWeek    = self::getCreateAt($record)->startOfWeek()->format('Y-m-d');
        $endOfWeek      = self::getCreateAt($record)->endOfWeek()->format('Y-m-d');

        $this->addSummaryRecord($this->records[$startOfWeek .' - ' . $endOfWeek][$record->task_id], $record);
    }

    /**
     * @param $record
     */
    protected function groupByMonth($record)
    {
        $month = self::getCreateAt($record)->format('F');

        $this->addSummaryRecord($this->records[$month][$record->task_id], $record);
    }

    /**
     * @param $record
     */
    protected function groupByYear($record)
    {
        $year = self::getCreateAt($record)->year;

        $this->addSummaryRecord($this->records[$year][$record->task_id], $record);
    }

    /**
     *  sort records, two dimensions
     */
    protected function sortRecords()
    {
        switch ($this->groupBy) {
            case "By Day":
                $this->sortDescGroupByDate();
                $this->sortSummaryNested();
                $this->resetGroupRecordsKeys();
                break;
            case "By Week":
                $this->sortDescGroupByWeek();
                $this->sortSummaryNested();
                $this->resetGroupRecordsKeys();
                break;
            case "By Month":
                $this->sortDescGroupByMonth();
                $this->sortSummaryNested();
                $this->resetGroupRecordsKeys();
                break;
            case "By Year":
                $this->sortDescGroupByYear();
                $this->sortSummaryNested();
                $this->resetGroupRecordsKeys();
                break;
            default:
                $this->sortDescByDate($this->records);
                $this->convertTime($this->records);
                break;
        }
    }

    /**
     *  sort nested records
     */
    private function sortSummaryNested()
    {
        foreach ($this->records as $key => $value) {
            $this->convertTime($value);
            $this->sortDescByDate($this->records[$key]);
        }
    }

    private function convertTime(&$values)
    {
        foreach ($values as $record ) {
            $this->calcMinutesHours($record->total_time_used);
            $this->calcMinutesHours($record->total_billed_time);
            $this->calcMinutesHours($record->billed_time);
            $this->calcMinutesHours($record->unbilled_time_total);
        }
    }

    /**
     * @param $record
     */
    protected function noneGroupBy($record)
    {
        $this->addSummaryRecord($this->records[$record->task_id], $record);
    }

    /**
     * @param $recordValue
     * @param $record
     */
    private function addSummaryRecord(&$recordValue, $record)
    {
        if (!is_null($recordValue)) {
            $userFullName = $record->user_name . ' ' . $record->user_last_name;

            if (!in_array($userFullName, $recordValue->users_name)){
                array_push($recordValue->users_name, $userFullName);
            }

            if ($recordValue->billing_status_name !== $record->billing_status_name) {
                $recordValue->billing_status_name   = "Mixed";
                $recordValue->billing_status_alias  = "Mixed";
                $recordValue->billing_status_color  = "";
            }

            $recordValue->total_time_used->add($record->{$this->timeUsed});
            $recordValue->total_billed_time->add($record->{$this->timeBill});
            $recordValue->billed_time->add($record->billed_time);
            $recordValue->unbilled_time_total->add($record->unbilled_time_total);
        } else {
            $newRecord = new stdClass;

            $record->users_name = [$record->user_name . ' ' . $record->user_last_name];
            $record->total_time_used = CarbonInterval::create(0);
            $record->total_billed_time = CarbonInterval::create(0);
            $record->total_time_used->add($record->{$this->timeUsed});
            $record->total_billed_time->add($record->{$this->timeBill});

            $objectVars = get_object_vars($record);

            foreach ($objectVars as $key => $value) {
                if (in_array($key, $this->recordProperties)) {
                    $newRecord->{$key} = $value;
                }
            }

            $recordValue = $newRecord;
        }
    }
}
