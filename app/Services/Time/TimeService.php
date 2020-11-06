<?php

namespace App\Services\Time;

use App\Models\Timer;
use App\Services\BaseService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;

class TimeService extends BaseService
{
    const CONVERT_FORMAT = [
        'YYYY'  => 'Y',
        'YY'    => 'y',
        'MMMM'  => 'F',
        'MMM'   => 'M',
        'MM'    => 'm',
        'M'     => 'n',
        'DDD'   => 'z',
        'DD'    => 'd',
        'D'     => 'j',
        'dddd'  => 'l',
        'ddd'   => 'D',
        'E'     => 'N',
        'o'     => 'S',
        'e'     => 'w',
        'W'     => 'W',
        'a'     => 'a',
        'A'     => 'A',
        'h'     => 'g',
        'HH'    => 'H',
        'H'     => 'G',
        'hh'    => 'h',
        'mm'    => 'i',
        'SSS'   => 'u',
        'ss'    => 's',
        'zz'    => 'e',
        'X'     => 'U',
    ];






    /**
     * Convert string time to carbon interval
     *
     * @param string $time
     *
     * @return CarbonInterval
     */
    public static function stringToCarbonInterval(string $time = '0:0:0') : CarbonInterval
    {
        $time = explode(':', $time);

        if (count($time)) {
            $seconds   = isset($time[2]) ? (integer)$time[2] : 0;
            $minutes   = isset($time[1]) ? (integer)$time[1] : 0;
            $hours     = isset($time[0]) ? (integer)$time[0] : 0;

            return self::toCarbonInterval($seconds, $minutes, $hours);
        }

        return CarbonInterval::hours($time);
    }

    /**
     * Diff caron interval
     *
     * @param CarbonInterval $old
     * @param CarbonInterval $new
     *
     * @return CarbonInterval
     */
    public static function diffCarbonIntervals(CarbonInterval $old, CarbonInterval $new) : CarbonInterval
    {
        /** @var Carbon $date1 */
        $date1 = now()->addDay($old->dayz)->addHours($old->hours)->addMinutes($old->minutes);

        /** @var Carbon $date2 */
        $date2 = now()->addDay($new->dayz)->addHours($new->hours)->addMinutes($new->minutes)->addSeconds($new->seconds);

        /** @var CarbonInterval $diffSeconds */
        $diff = $date2->diffInRealSeconds($date1, false);

        if ($diff > 0) {
            return self::toCarbonInterval($diff);
        }

        return CarbonInterval::create(0);
    }

    /**
     * @param CarbonInterval $interval
     *
     * @return CarbonInterval
     */
    public static function roundCarbonInterval(CarbonInterval $interval) : CarbonInterval
    {
        self::toCarbonInterval($interval->s, $interval->i, $interval->h);
    }

    /**
     * @param int $seconds
     * @param int $minutes
     * @param int $hours
     *
     * @return CarbonInterval
     */
    public static function toCarbonInterval(int $seconds = 0, int $minutes = 0, int $hours = 0)
    {
        $minutes        = floor($seconds / 60) + $minutes;

        $carbonSeconds  = $seconds % 60;
        $carbonMinutes  = $minutes % 60;
        $carbonHours    = floor($minutes / 60) + $hours;

        return CarbonInterval::hours($carbonHours)->minutes($carbonMinutes)->seconds($carbonSeconds);
    }

    /**
     * Diff times
     *
     * @param string $old
     * @param string $new
     *
     * @return int seconds
     */
    public static function diffTimes(string $old, string $new) : int
    {
        return Carbon::parse($new)->diffInSeconds(Carbon::parse($old));
    }

    /**
     * @param $timer
     *
     * @return bool|\DateInterval
     */
    public static function getSumTimeByTimer($timer)
    {
        $start  = Carbon::parse($timer->start_time);
        $end    = Carbon::parse($timer->end_time);

        $pauses = $timer->pauses ?? $timer->timerPauses ?? [];

        foreach ($pauses as $pause) {
            $startPause = Carbon::parse($pause->start_time);
            $endPause   = Carbon::parse($pause->end_time);

            $end = $end->sub($startPause->diff($endPause));
        }

        return $start->diff($end);
    }

    /**
     * @param \DateInterval|CarbonInterval $time
     * @param int                          $precision
     *
     * @return float
     * @throws \Exception
     */
    public static function getTotalHours($time, $precision = 2)
    {
        if (is_null($time)) {
            return null;
        }

        if ($time instanceof \DateInterval) {
            $time = CarbonInterval::instance($time);
        }

        if ($time instanceof CarbonInterval) {
            return round($time->totalHours, $precision);
        }

        throw new \Exception('Invalid time format, expect to be DateInterval or CarbonInterval');
    }

    /**
     * @param \DateInterval|CarbonInterval $time
     * @param int                          $precision
     *
     * @return float
     * @throws \Exception
     */
    public static function getTotalMinutes($time = null, $precision = 0)
    {
        if (is_null($time)) {
            return null;
        }

        if ($time instanceof \DateInterval) {
            $time = CarbonInterval::instance($time);
        }

        if ($time instanceof CarbonInterval) {
            $fig = pow(10, $precision);

            return ceil($time->totalMinutes * $fig) / $fig;
        }

        throw new \Exception('Invalid time format, expect to be DateInterval or CarbonInterval');
    }

    /**
     * Add to the start date the time
     *
     * @param string|null $startDate
     * @param string      $time
     *
     * @return string
     */
    public static function addTimeToDate(string $startDate = null, string $time = '') : string
    {
        $date = $startDate ? Carbon::parse($startDate) : Carbon::now();
        $time = self::stringToCarbonInterval($time);

        return $date->subHours($time->hours)->subMinutes($time->minutes)->subSeconds($time->seconds)->toDateTimeString();
    }


    /**
     * @param string|Carbon $time
     * @param int           $utcOffset
     *
     * @return Carbon|null
     */
    public static function toUserLocalTime($time, int $utcOffset = null) :?Carbon
    {
        if (is_null($utcOffset)) {
            $utcOffset = Auth::utcOffset();
        }

        if (!$time instanceof Carbon) {
            $time = Carbon::parse($time);
        }

        if ($time) {
            if ($utcOffset >= 0) {
                return $time->subMinutes($utcOffset);
            } else {
                return $time->addMinutes($utcOffset);
            }
        }

        return null;
    }

    /**
     * @param string|Carbon $date
     * @param string|Carbon $start
     * @param string|Carbon $end
     *
     * @return bool
     */
    public static function dateBetween($date, $start, $end) : bool
    {
        if ($date instanceof Carbon) {
            $date = $date->toDateTimeString();
        }

        if ($start instanceof Carbon) {
            $start = $start->toDateTimeString();
        }

        if ($end instanceof Carbon) {
            $end = $end->toDateTimeString();
        }

        return Carbon::parse($date)->between(Carbon::parse($start), Carbon::parse($end));
    }

    /**
     * @param Timer|\stdClass   $timer
     * @param int               $round
     *
     * @return string
     * @throws \Exception
     */
    public static function getRoundTrackedTimeByTimer($timer, $round = 15)
    {
        $trackedTime = self::getSumTimeByTimer($timer);
        $trackedTime = self::getTotalMinutes($trackedTime);
        $trackedTime = ceil($trackedTime / $round) * $round;

        return CarbonInterval::hours(floor($trackedTime / 60))->minutes($trackedTime % 60)->format("%H:%I:%S");
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public static function convertMomentToPHPFormat(string $format)
    {
        return strtr($format, self::CONVERT_FORMAT);
    }
}
