<?php

namespace App\Collections;

use App\Services\Time\TimeService;
use Illuminate\Support\Collection;

class TimerCollection extends BaseCollection
{
    /**
     * Set tracked time
     *
     * @param Collection $collection
     *
     * @return $this
     */
    public function setTrackedTime(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            $value['time'] = TimeService::getSumTimeByTimer((object) array_merge($value, [
                'pauses' => $collection->get($value['id']) ?? collect()
            ]));

            return $value;
        });
    }

    /**
     * @return static
     */
    public function getActiveTimer()
    {
        return $this->filter(function ($timer) {
            if (!is_null($timer->start_time) && is_null($timer->end_time)) {
                if ($timer->pauses) {
                    foreach ($timer->pauses as $pause) {
                        if (is_null($pause->end_time)) {
                            return false;
                        }
                    }
                }

                return true;
            }

            return false;
        })->first();
    }
}