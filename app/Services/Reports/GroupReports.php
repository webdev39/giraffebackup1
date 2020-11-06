<?php

namespace App\Services\Reports;

use App\Services\Time\TimeService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use stdClass;

/**
 * Class GroupReports
 *
 * @package App\Services\Reports
 */
abstract class GroupReports
{
    protected $groupBy;
    protected $records;
    protected $timeUsed;
    protected $timeBill;
    protected $totalTimeUsed;
    protected $totalTimeBill;
    protected $totalUnbilledTime;
    protected $totalBilledTime;

    /**
     * GroupReports constructor.
     *
     * @param string $groupBy
     * @param string $time_used
     * @param string $time_bill
     */
    public function __construct(string $groupBy, string $time_used, string $time_bill)
    {
        $this->groupBy              = $groupBy;
        $this->timeUsed             = $time_used;
        $this->timeBill             = $time_bill;
        $this->totalTimeUsed        = CarbonInterval::create(0);
        $this->totalTimeBill        = CarbonInterval::create(0);
        $this->totalUnbilledTime    = CarbonInterval::create(0);
        $this->totalBilledTime      = CarbonInterval::create(0);
        $this->records              = [];
    }

    /**
     * @param $record
     */
    public function addRecord($record)
    {
        switch ($this->groupBy) {
            case "By Day":
                $this->groupByDay($record);
                break;
            case "By Week":
                $this->groupByWeek($record);
                break;
            case "By Month":
                $this->groupByMonth($record);
                break;
            case "By Year":
                $this->groupByYear($record);
                break;
            default:
                $this->noneGroupBy($record);
                break;
        }
    }

    protected function sortRecords()
    {
        switch ($this->groupBy) {
            case "By Day":
                $this->sortDescGroupByDate();
                $this->resetGroupRecordsKeys();
                break;
            case "By Week":
                $this->sortDescGroupByWeek();
                $this->resetGroupRecordsKeys();
                break;
            case "By Month":
                $this->sortDescGroupByMonth();
                $this->resetGroupRecordsKeys();
                break;
            case "By Year":
                $this->sortDescGroupByYear();
                $this->resetGroupRecordsKeys();
                break;
            default:
                $this->sortDescByDate($this->records);
                break;
        }
    }

    protected function sortDescByDate(&$records)
    {
        if (!isset($a->end_time) || !isset($a->end_time)) {
            return 1;
        }

        usort($records, function($a,$b) {
            if (Carbon::parse($a->end_time)->lt(Carbon::parse($b->end_time))) {
                return 1;
            }else{
                return -1;
            }
        });
    }

    protected function sortDescGroupByDate()
    {
        uksort($this->records, function($a,$b){
            if (Carbon::parse($a)->lt(Carbon::parse($b))) {
                return 1;
            }else{
                return -1;
            }
        });
    }

    protected function sortDescGroupByWeek()
    {
        uksort($this->records, function($a,$b) {
            $a = explode(' ',$a);
            $b = explode(' ',$b);

            if (Carbon::parse($a[0])->lt(Carbon::parse($b[0]))) {
                return 1;
            } else {
                return -1;
            }
        });
    }

    protected function sortDescGroupByMonth()
    {
        uksort($this->records, function($a,$b) {
            if (Carbon::parse($a)->lt(Carbon::parse($b))) {
                return 1;
            }else{
                return -1;
            }
        });
    }

    protected function sortDescGroupByYear()
    {
        uksort($this->records, function($a,$b) {
            if ((int)$a < (int) $b){
                return 1;
            }else{
                return -1;
            }
        });
    }

    protected function resetGroupRecordsKeys()
    {
        foreach ($this->records as $key => $records) {
            $this->records[$key] = array_values($records);
        }
    }


    /**
     * @param $record
     */
    abstract protected function groupByDay($record);


    /**
     * @param $record
     */
    abstract protected function groupByWeek($record);


    /**
     * @param $record
     */
    abstract protected function groupByMonth($record);


    /**
     * @param $record
     */
    abstract protected function groupByYear($record);


    /**
     * @param $record
     */
    abstract protected function noneGroupBy($record);

    /**
     * @param $record
     */
    public function calcRecordTotal($record)
    {
        if (isset($record->time_used)) {
            $this->totalTimeUsed->add($record->time_used);
        } else if (isset($record->total_time_used)) {
            $this->totalTimeUsed->add($record->total_time_used);
        }

        if (isset($record->time_bill)) {
            $this->totalTimeBill->add($record->time_bill);
        } else if (isset($record->total_billed_time)) {
            $this->totalTimeBill->add($record->total_billed_time);
        }

        if (isset($record->unbilled_time_total)) {
            $this->totalUnbilledTime->add($record->unbilled_time_total);
        }

        if (isset($record->billed_time)) {
            $this->totalBilledTime->add($record->billed_time);
        }
    }

    /**
     * @param $time
     *
     * @return CarbonInterval|null
     */
    protected function calcTimeInterval($time) : ?CarbonInterval
    {
        list($hours, $minutes, $seconds) = explode(':', $time);

        return CarbonInterval::create(0, 0, 0, 0, intval($hours), intval($minutes), intval($seconds));
    }

    /**
     * @param CarbonInterval $time
     *
     * @return $this
     */
    protected function calcMinutesHours(CarbonInterval &$time)
    {
        $timeMinutes = $time->minutes % 60;
        $timeHours = floor(($time->minutes - $timeMinutes) / 60) + $time->hours + $time->dayz * 24;

        if ($timeMinutes == 60) {
            $timeMinutes = 0;
            $timeHours++;
        }

        $time->minutes($timeMinutes);
        $time->hours($timeHours);
        $time->dayz(0);

        return $this;
    }

    /**
     * @param CarbonInterval $time
     *
     * @return string
     */
    protected function transformToProperString(CarbonInterval $time) : string
    {
        return $time->hours . ':' . $time->minutes;
    }

    /**
     * @return CarbonInterval|null
     */
    public function getTotalTimeUsed(): ?CarbonInterval
    {
        return $this->calcMinutesHours($this->totalTimeUsed)->totalTimeUsed;
    }

    /**
     * @return CarbonInterval|null
     */
    public function getTotalTimeBill(): ?CarbonInterval
    {
        return $this->calcMinutesHours($this->totalTimeBill)->totalTimeBill;
    }

    /**
     * @return CarbonInterval|null
     */
    public function getTotalUnbilledTime(): ?CarbonInterval
    {
        return $this->calcMinutesHours($this->totalUnbilledTime)->totalUnbilledTime;
    }

    /**
     * @return CarbonInterval|null
     */
    public function getTotalBilledTime(): ?CarbonInterval
    {
        return $this->calcMinutesHours($this->totalBilledTime)->totalBilledTime;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
         $this->sortRecords();

         return $this->records;
    }

    /**
     * @param $record
     *
     * @return Carbon|null
     */
    protected static function getCreateAt($record)
    {
        return TimeService::toUserLocalTime($record->end_time);
    }
}
