<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\Group;

/**
 * Class SavedGroupEvent
 *
 * @package App\Events\Eloquent
 */
class SavedGroupEvent extends BaseEloquentEvent
{
    /** @var Group */
    public $model;
}