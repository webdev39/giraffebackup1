<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\BaseModel;
use App\Models\Task;

/**
 * Class SavedTaskEvent
 *
 * @package App\Events\Eloquent
 */
class SavedTaskEvent extends BaseEloquentEvent
{
    /** @var Task */
    public $model;

    /** @var int */
    public $draft;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        parent::__construct($model);

        $this->draft = $model->getOriginal('draft');
    }
}