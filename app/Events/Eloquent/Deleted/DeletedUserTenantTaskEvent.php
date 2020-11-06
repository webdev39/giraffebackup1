<?php

namespace App\Events\Eloquent\Deleted;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\BaseModel;
use App\Models\UserTenantTask;

/**
 * Class DeletedUserTenantTaskEvent
 *
 * @package App\Events\Eloquent
 */
class DeletedUserTenantTaskEvent  extends BaseEloquentEvent
{
    /** @var UserTenantTask */
    public $model;

    /** @var bool */
    public $attach;

    /** @var User */
    public $detachUser;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->user  = \Auth::user();
        $this->attach = false;

        $this->model = new \stdClass();
        $this->model->task = $model->task;
        $this->model->user = $model->userTenant->user;

        if($model instanceof UserTenantTask) {
            $this->detachUser = $model->getUser();
        }
    }
}