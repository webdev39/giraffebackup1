<?php

namespace App\Listeners;

use App\Broadcasting\PersonalEvents;
use App\Models\Timer;
use Illuminate\Support\Facades\Auth;

class TimerEventSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('timer.start','App\Listeners\TimerEventSubscriber@onTimerStart');
        $events->listen('timer.pause','App\Listeners\TimerEventSubscriber@onTimerPause');
        $events->listen('timer.continue','App\Listeners\TimerEventSubscriber@onTimerContinue');
        $events->listen('timer.stop','App\Listeners\TimerEventSubscriber@onTimerStop');
        $events->listen('timer.update','App\Listeners\TimerEventSubscriber@onTimerUpdate');
    }

    /**
     * @param int $timerId
     */
    public function onTimerStart(int $timerId)
    {
        self::sendTimerEvent('timer.start', $timerId);
    }

    /**
     * @param int $timerId
     */
    public function onTimerPause(int $timerId)
    {
        self::sendTimerEvent('timer.pause', $timerId);
    }

    /**
     * @param int $timerId
     */
    public function onTimerContinue(int $timerId)
    {
        self::sendTimerEvent('timer.continue', $timerId);
    }

    /**
     * @param int $timerId
     */
    public function onTimerStop(int $timerId)
    {
        self::sendTimerEvent('timer.stop', $timerId);
    }

    /**
     * @param int $timerId
     */
    public function onTimerUpdate(int $timerId)
    {
        self::sendTimerEvent('timer.update', $timerId);
    }

    /**
     * @param string $event
     * @param int    $timerId
     */
    private static function sendTimerEvent(string $event, int $timerId)
    {
        $timer = app('TimerRepo')->find($timerId);

        event(new PersonalEvents($event, $timer, Auth::user()));
    }
}
