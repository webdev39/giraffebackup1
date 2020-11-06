<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 28.03.18
 * Time: 14:32
 */

namespace App\Services\Reports;


use Carbon\Carbon;

class GroupReportsDetail extends GroupReports
{
    /**
     * GroupReportsDetail constructor.
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
        $this->records[$day][] = $record;
    }

    /**
     * @param $record
     */
    protected function groupByWeek($record)
    {
        $startOfWeek    = self::getCreateAt($record)->startOfWeek()->format('Y-m-d');
        $endOfWeek      = self::getCreateAt($record)->endOfWeek()->format('Y-m-d');
        $this->records[$startOfWeek .' - ' . $endOfWeek][] = $record;
    }

    /**
     * @param $record
     */
    protected function groupByMonth($record)
    {
        $month = self::getCreateAt($record)->format('Y-m');
        $this->records[$month][] = $record;
    }

    /**
     * @param $record
     */
    protected function groupByYear($record)
    {
        $year = self::getCreateAt($record)->year;
        $this->records[$year][] = $record;
    }

    /**
     * @param $record
     */
    protected function noneGroupBy($record)
    {
        $this->records[] = $record;
    }
}