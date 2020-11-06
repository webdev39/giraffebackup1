<?php

namespace App\Events\Eloquent\Saved;

use App\Models\BaseModel;
use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\UserTenantTask;

/**
 * Class SavedUserTenantTaskEvent
 *
 * @package App\Events\Eloquent
 */
class SavedUserTenantTaskEvent extends BaseEloquentEvent
{
    /** @var UserTenantTask */
    public $model;

    /** @var bool */
    public $attach;

    /** @var User */
    public $attachUser;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        parent::__construct($model);

        $this->attach = true;

        if($model instanceof UserTenantTask) {
            $this->attachUser = $model->getUser();
        }
    }
}