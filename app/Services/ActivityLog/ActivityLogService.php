<?php

namespace App\Services\ActivityLog;

use App\Collections\ActivityCollection;
use App\Collections\CommentCollection;
use App\Collections\UserCollection;
use App\Events\Eloquent\BaseEloquentEvent;
use App\Events\Eloquent\Saved\DeletedNotificationSubscriptionEvent;
use App\Models\BaseModel;
use App\Models\Board;
use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use App\Repositories\ActivityLogRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActivityLogService
{
    const USER_EVENT = [
        'task'  => [
            'done_by'       => ['closed', 'opened'],
            'is_archive'    => ['archived', 'unarchived']
        ],
        'group' => [
            'is_archive'    => ['archived', 'unarchived']
        ],
        'board' => [
            'is_archive'    => ['archived', 'unarchived']
        ],
    ];

    /**
     * @var ActivityLogRepositoryEloquent
     */
    private $activityLogRepo;

    /**
     * ActivityLogService constructor.
     */
    public function __construct()
    {
        $this->activityLogRepo = app('ActivityLogRepo');
    }

    /**
     * @param int $taskId
     *
     * @return mixed
     */
    public function getActivityLogByTaskId(int $taskId)
    {
        return $this->activityLogRepo->getActivityLogByTaskId($taskId);
    }

    /**
     * @param int $boardId
     *
     * @return mixed
     */
    public function getActivityLogByBoardId(int $boardId)
    {
        return $this->activityLogRepo->getActivityLogByBoardId($boardId);
    }

    /**
     * @param int $groupId
     *
     * @return mixed
     */
    public function getActivityLogByGroupId(int $groupId)
    {
        return $this->activityLogRepo->getActivityLogByGroupId($groupId);
    }

    /**
     * @return mixed
     */
    public function getUserActivityLog()
    {
        return $this->activityLogRepo->getUserActivityLog();
    }

    /**
     * @param Collection $activityLogs
     *
     * @return Collection
     */
    public function addActivityLogRelations(Collection $activityLogs) : Collection
    {
        $userIds = $activityLogs->pluck('user_id')->unique()->toArray();
        $users = app('UserRepo')->getUsersByIds($userIds);
        $users = UserCollection::make($users);

        $activityLogs = ActivityCollection::make($activityLogs);
        $activityLogs->setUser($users);

        return $activityLogs;
    }

    public static function taskSubscribeAndAttachAction($event, Task $task, User $receiver)
    {
        $board   = $task->board->first();
        $action  = 'assigned_and_subscribed';

        $message = __('activity.task.action.assigned_and_subscribed', [
            'action'    => 'attached and subscribed',
            'sender'    => $event->sender->full_name,
            'receiver'  => $receiver->full_name,
            'task'      => $task->name,
        ]);

        self::saveActivityLog($task, $event->sender, $message, [
            'action'    => $action,
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $board->group->id,
        ]);
    }

    /**
     * @param      $event
     * @param Task $task
     * @param User $receiver
     */
    public static function taskSubscribeAction($event, Task $task, User $receiver)
    {
        $board   = $task->board->first();
        $action  = $event->attach ? 'subscribed' : 'unsubscribed';

        $message = __('activity.task.action.attach', [
            'action'    => $action,
            'sender'    => $event->user->full_name,
            'receiver'  => $receiver->full_name,
            'task'      => $task->name,
        ]);

        self::saveActivityLog($task, $event->user, $message, [
            'action'    => $action,
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $board->group->id,
        ]);
    }

    public function taskCreated(Task $task, User $user)
    {
        $board = $task->board->first();
        self::saveActivityLog($task, $user, 'Task created by '.$task->creator->full_name, [
            'action'    => 'created',
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $board->id,
        ]);
    }

    /**
     * @param      $event
     * @param Task $task
     * @param User $receiver
     */
    public static function taskAttachAction($event, Task $task, User $receiver)
    {
        $board   = $task->board->first();
        $action  = $event->attach ? 'assigned' : 'unassigned';
        $message = __('activity.task.action.attach', [
            'action'    => $action,
            'sender'    => $event->user->full_name,
            'receiver'  => $receiver->full_name,
            'task'      => $task->name,
        ]);

        self::saveActivityLog($task, $event->user, $message, [
            'action'    => $action,
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $board->group->id,
        ]);
    }

    /**
     * @param      $event
     * @param Task $task
     * @param User $receiver
     */
    public static function taskDetachAction($event, Task $task, User $receiver)
    {
        $board   = $task->board->first();
        $action  = $event instanceof DeletedNotificationSubscriptionEvent ? 'unsubscribed' : 'unassigned';

        $message = __('activity.task.action.attach', [
            'action'    => $action,
            'sender'    => $event->user->full_name,
            'receiver'  => $receiver->full_name,
            'task'      => $task->name,
        ]);
        
        self::saveActivityLog($task, $event->user, $message, [
            'action'    => $action,
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $board->group->id,
        ]);
    }

    /**
     * @param BaseEloquentEvent $event
     * @param Task              $task
     */
    public static function changeTaskAction(BaseEloquentEvent $event, Task $task)
    {
        $board = $task->board->first();
        $group = $board->group;

        foreach ($event->activity as $field => $option) {
            $action = self::getUserEvent($event->model->logName, $field, $option['new_value']);
            $option = self::getTaskOption($field, $option, $task, $board, $group);

            if($field == 'sort_weight') {
                $action = $option['old_value'] > $option['new_value']
                    ? 'decreased'
                    : 'increased';
            }
            if($field == 'deadline') {
                if(empty($option['old_value'])) {
                    $option['new_value'] = !empty($option['new_value']) ? Carbon::parse($option['new_value'])->toDateString() : '``';
                    $message = __('activity.task.set.deadline', array_merge($option, [
                        'action'    => $action,
                        'task'      => $task->name,
                        'sender'    => $event->user->full_name,
                    ]));
                } else {
                    $option['old_value'] = Carbon::parse($option['old_value'])->toDateString();
                    $option['new_value'] = !empty($option['new_value']) ? Carbon::parse($option['new_value'])->toDateString() : '``';
                    $message = __('activity.task.changed.deadline', array_merge($option, [
                        'action'    => $action,
                        'task'      => $task->name,
                        'sender'    => $event->user->full_name,
                    ]));
                }
            } elseif($field == 'description') {
                if(empty($option['old_value'])) {
                    $message = __('activity.task.set.'.$field, array_merge($option, [
                        'action'    => $action,
                        'task'      => $task->name,
                        'sender'    => $event->user->full_name,
                    ]));
                } else {
                    $description = __('activity.task.changed.description');
                    $arMessage = [];
                    
                    foreach ($description as $key => $desc) {
                        $arMessage[$key] = __('activity.task.changed.description.'.$key, array_merge($option, [
                            'action'    => $action,
                            'task'      => $task->name,
                            'sender'    => $event->user->full_name,
                        ]));
                    }
                    $message = json_encode($arMessage);
                }
            } else {
                $message = __('activity.task.changed.'.$field, array_merge($option, [
                    'action'    => $action,
                    'task'      => $task->name,
                    'sender'    => $event->user->full_name,
                ]));
            }

            self::saveActivityLog($task, $event->user, $message, [
                'action'    => $action,
                'field'     => $field,
                'new'       => $option['new_value'],
                'old'       => $option['old_value'],
                'task_id'   => $task->id,
                'board_id'  => $board->id,
                'group_id'  => $group->id,
            ]);
        }
    }

    /**
     * @param Task        $task
     * @param User        $user
     * @param string      $action
     * @param string      $message
     * @param string|null $field
     */
    public static function customTaskAction(Task $task, User $user = null,  string $message = null,  string $action = null, string $field = null)
    {
        $board = $task->board->first();
        $group = $board->group;

        self::saveActivityLog($task, $user, $message, [
            'action'    => $action,
            'field'     => $field,
            'task_id'   => $task->id,
            'board_id'  => $board->id,
            'group_id'  => $group->id,
        ]);
    }

    /**
     * @param BaseEloquentEvent $event
     * @param Board             $board
     */
    public static function changeBoardAction(BaseEloquentEvent $event, Board $board)
    {
        $group = $board->group;

        foreach ($event->activity as $field => $option) {
            $action  = self::getUserEvent($event->model->logName, $field, $option['new_value']);

            $message = __('activity.board.changed.'.$field, [
                'action'    => $action,
                'board'     => $board->name,
                'group'     => $group->name,
                'sender'    => $event->user->full_name,
                'new'       => $option['new_value'],
                'old'       => $option['old_value'],
            ]);

            self::saveActivityLog($board, $event->user, $message, [
                'action'    => $action,
                'field'     => $field,
                'board_id'  => $board->id,
                'group_id'  => $group->id,
            ]);
        }
    }

    /**
     * @param BaseEloquentEvent $event
     * @param Group             $group
     */
    public static function changeGroupAction(BaseEloquentEvent $event, Group $group)
    {
        foreach ($event->activity as $field => $option) {
            $action  = self::getUserEvent($event->model->logName, $field, $option['new_value']);

            $message = __('activity.group.changed.'.$field, [
                'action'    => $action,
                'group'     => $group->name,
                'sender'    => $event->user->full_name,
                'new'       => $option['new_value'],
                'old'       => $option['old_value'],
            ]);

            self::saveActivityLog($group, $event->user, $message, [
                'action'    => $action,
                'field'     => $field,
                'group_id'  => $group->id,
            ]);
        }
    }

    public static function saveActivityLog(BaseModel $model, User $causedBy = null, string $message = null, array $properties = [])
    {
        $activity = activity()->useLog($model->logName)->performedOn($model)->withProperties($properties);

        if ($causedBy) {
            $activity->causedBy($causedBy);
        }

        $activity = $activity->log($message);
        $activity->group_id = @$activity->properties['group_id'];
        $activity->board_id = @$activity->properties['board_id'];
        $activity->task_id = @$activity->properties['task_id'];
        $activity->action = @$activity->properties['action'];
        $activity->field = @$activity->properties['field'];
        $activity->save();

        return $activity;
    }

    /**
     * @param string $table
     * @param string $field
     * @param        $value
     *
     * @return string
     */
    private static function getUserEvent(string $table, string $field, $value) : string
    {
        if (isset(self::USER_EVENT[$table][$field])) {
            return $value ? self::USER_EVENT[$table][$field][0] : self::USER_EVENT[$table][$field][1];
        }

        return 'changed';
    }

    /**
     * @param string $field
     * @param array  $options
     * @param Task   $task
     * @param Board  $board
     * @param Group  $group
     *
     * @return array
     */
    private static function getTaskOption(string $field, array $options, Task $task, Board $board, Group $group) : array
    {
        if ($field == 'priority_id') {
            $getNewName = app('PriorityRepo')->findWhere(['id' => $options['new_value']], ['name'])->first();
            $options['new_name'] = is_null($getNewName) ? $getNewName : $getNewName->name;
            $getOldName = app('PriorityRepo')->findWhere(['id' => $options['old_value']], ['name'])->first();
            $options['old_name'] = is_null($getOldName) ? $getOldName : $getOldName->name;

            return $options;
        }

        return $options;
    }
}
