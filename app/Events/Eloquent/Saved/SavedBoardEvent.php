<?php

namespace App\Events\Eloquent\Saved;

use App\Events\Eloquent\BaseEloquentEvent;
use App\Models\Board;

/**
 * Class SavedBoardEvent
 *
 * @package App\Events\Eloquent
 */
class SavedBoardEvent extends BaseEloquentEvent
{
    /** @var Board */
    public $model;
}