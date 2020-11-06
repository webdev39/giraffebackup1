<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TaskSubscriptionAndAssignmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Task
     */
    public $task;
    /**
     * @var User
     */
    public $user;
    /**
     * @var User
     */
    public $sender;

    /**
     * TaskSubscriptionAndAssignmentEvent constructor.
     * @param User $sender
     * @param Task $task
     * @param User $user
     */
    public function __construct(User $sender, Task $task, User $user)
    {
        //
        $this->task = $task;
        $this->user = $user;
        $this->sender = $sender;
    }
}
