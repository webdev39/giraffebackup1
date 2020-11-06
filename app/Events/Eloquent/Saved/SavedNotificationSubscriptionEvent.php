<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\BaseModel;
use App\Models\NotificationSubscription;

/**
 * Class SavedNotificationSubscriptionEvent
 *
 * @package App\Events\Eloquent\Saved
 */
class SavedNotificationSubscriptionEvent extends BaseEloquentEvent
{
    /** @var NotificationSubscription */
    public $model;

    /** @var bool */
    public $subscribed;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        parent::__construct($model);

        $this->subscribed = true;
    }
}
