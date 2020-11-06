<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\BaseModel;

/**
 * Class DeletedNotificationSubscriptionEvent
 *
 * @package App\Events\Eloquent\Saved
 */
class DeletedNotificationSubscriptionEvent extends BaseEloquentEvent
{
    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->user  = \Auth::user();
        $this->subscribed = false;

        $this->model = new \stdClass();
        $this->model->task = $model->task;
        $this->model->user = $model->user;
    }
}
