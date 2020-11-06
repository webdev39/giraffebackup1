<?php

namespace App\Events\Eloquent\Saved;

use App\Models\BaseModel;

class SavedSubTaskEvent
{
    /**
     * @var BaseModel
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }
}
