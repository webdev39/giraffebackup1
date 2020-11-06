<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BudgetNotification;
use App\Notifications\DeadlineNotification;
use App\Services\Task\TaskService;
use App\Services\Timer\TimerService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class AdditionalTaskNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to send notifications to users whose task has exceeded the deadline';

    /**
     * @var TaskService
     */
    protected $taskService;

    /**
     * @var TimerService
     */
    protected $timerService;

    /**
     * @var Collection
     */
    protected $company;

    /**
     * Create a new command instance.
     *
     * @param TaskService $taskService
     * @param TimerService $timerService
     */
    public function __construct(TaskService $taskService, TimerService $timerService)
    {
        parent::__construct();
        $this->taskService = $taskService;
        $this->timerService = $timerService;

        $this->company = (object)[
            'id' => 0,
            'name' => config('company.settings.name'),
            'last_name' => '',
            'avatar' => config('company.settings.logo')
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sendDeadlineNotifications();
        $this->sendBudgetNotifications();
    }

    /**
     * This method for sending notifications that have expired deadline
     */
    private function sendDeadlineNotifications()
    {
        $tasks = $this->taskService->getTaskHaveOverDeadline();

        $tasks->each(function ($itemTask, $keyTask) {
            if ($itemTask->taskSubscribers->isNotEmpty()) {
                $itemTask->taskSubscribers->each(function ($itemSub, $keySub) use ($itemTask) {
                    $itemSub->user->notify(new DeadlineNotification(
                        User::find($itemTask->creator_id) ?? $this->company, $itemTask, $itemSub->user)
                    );
                });
            }
        });
    }

    /**
     * This method for sending notifications that have expired deadline
     */
    private function sendBudgetNotifications()
    {
        $tasks = $this->taskService->getTaskHaveOverBudget();

        $tasks->each(function ($itemTask, $keyTask) {
            $taskBudget = $itemTask->budget;
            if ($itemTask->taskSubscribers->isNotEmpty()) {
                $itemTask->taskSubscribers->each(function ($itemSub, $keySub) use ($itemTask, $taskBudget) {
                    $timers = $this->timerService->getTimerByUserTenantTaskId($itemSub, $itemTask->id);
                    $message = $this->timerService->overBudget($taskBudget, $timers);
                    if ($message) {
                        $itemSub->user->notify(new BudgetNotification(
                            User::find($itemTask->creator_id) ?? $this->company,
                            $itemTask,
                            $itemSub->user,
                            $message)
                        );
                    }
                });
            }
        });
    }
}
