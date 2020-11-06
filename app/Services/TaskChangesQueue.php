<?php

namespace App\Services;


use App\Events\TaskChangeToNotificationEvent;
use App\Models\Task;
use App\Models\TaskChangesQueue as TaskChangesQueueModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class TaskChangesQueue
 * @package App\Services
 */
class TaskChangesQueue
{
    /**
     * @param User $sender
     * @param Task $task
     * @param string $field
     * @param SupportCollection $subscribers
     * @param string $notificationClass
     * @param string $newValue
     * @param string $oldValue
     * @return mixed
     */
    public function push(
        User $sender,
        Task $task,
        string $field,
        SupportCollection $subscribers,
        string $notificationClass,
        string $newValue = null,
        string $oldValue = null
    ) {
        return TaskChangesQueueModel::create([
            'sender_id' => $sender->id,
            'task_id' => $task->id,
            'field' => $field,
            'notification_class' => $notificationClass,
            'new_value' => (string) $newValue,
            'old_value' => (string) $oldValue,
            'subscribers' => $subscribers,
        ]);
    }

    public function convertChangesToNotifications(): void
    {
        /** @var Collection $allTasksChanges */
        $allTasksChanges = TaskChangesQueueModel::orderBy('created_at', 'asc')->get();
        foreach ($allTasksChanges->groupBy('sender_id') as $taskChangesBySender) {
            foreach($taskChangesBySender->groupBy('task_id') as $taskChanges) {
                foreach ($taskChanges->groupBy('field') as $field => $taskChangesByField) {
                    $this->createNotificationFromChanges(
                        $taskChangesByField->shift(),
                        $taskChangesByField->pop()
                    );

                    foreach ($taskChangesByField as $taskChange) {
                        $taskChange->delete();
                    }
                }
            }
        }
    }

    private function createNotificationFromChanges(TaskChangesQueueModel $initialState, TaskChangesQueueModel $currentState = null): void
    {
        $subscribers = $initialState->subscribers;
        $oldValue = $initialState->old_value;
        $newValue = $initialState->new_value;

        if($currentState) {
            $subscribers = $currentState->subscribers;
            $newValue = $currentState->new_value;
        }

        $initialState->delete();
        if($currentState) {
            $currentState->delete();
        }

        if($oldValue != $newValue) {
            event(new TaskChangeToNotificationEvent($initialState->sender ?? User::first(), $initialState->task, $subscribers, $initialState->field, $initialState->notification_class, $newValue, $oldValue));
        }
    }
}