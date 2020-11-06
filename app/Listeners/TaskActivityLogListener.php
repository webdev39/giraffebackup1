<?php

namespace App\Listeners;

use App\Events\ChangeActivityLogEvent;
use App\Events\Eloquent\ChangedTaskEvent;

class TaskActivityLogListener
{

    /**
     * @param ChangedTaskEvent $event
     */
    public function updateTaskLog(ChangedTaskEvent $event)
    {
        event(new ChangeActivityLogEvent($event->user, $event->model));
    }
}
