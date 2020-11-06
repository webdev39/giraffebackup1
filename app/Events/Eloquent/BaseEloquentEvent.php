<?php

namespace App\Events\Eloquent;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseEloquentEvent
 *
 * @package App\Events\Eloquent
 */
abstract class BaseEloquentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var User */
    public $user;

    /** @var Model */
    public $model;

    /** @var array */
    public $notify = [];

    /** @var array */
    public $activity = [];

    /** @var bool */
    public $wasRecentlyCreated;

    /**
     * Create a new event instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->user  = Auth::user();
        $this->model = $model;

        $this->wasRecentlyCreated = $model->wasRecentlyCreated;

        $this->setActivity();
        $this->setNotify();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * @param string|null $class
     * @param null        $value
     */
    public function setDefaultNotifyClass(string $class = null, $value = null)
    {
        $this->notify['default'] = [
            'class'     => $class,
            'old_value' => $value
        ];
    }

    private function setNotify()
    {
        foreach ($this->model->getDirty() as $key => $value) {
            if (isset($this->model->notifyAttributes[$key])) {
                $this->notify[$key] = [
                    'class'     => $this->model->notifyAttributes[$key],
                    'new_value' => $this->model->$key,
                    'old_value' => $this->model->getOriginal($key),
                ];
            }
        }
    }

    private function setActivity()
    {
        foreach ($this->model->getDirty() as $key => $value) {
            if (in_array($key, $this->model->logAttributes)) {
                $this->activity[$key] = [
                    'new_value' => $this->model->$key,
                    'old_value' => $this->model->getOriginal($key),
                ];
            }
        }
    }
}