<?php

namespace App\Http\Resources;

use App\Notifications\BudgetNotification;
use App\Notifications\ChangeHardBudgetNotification;
use App\Notifications\ChangeSoftBudgetNotification;
use App\Notifications\CommentLikeNotification;
use App\Notifications\CommentMentionNotification;
use App\Notifications\DeadlineNotification;
use App\Notifications\PrioritizeTaskNotification;
use App\Notifications\RenameBoardNotification;
use App\Notifications\RenameGroupNotification;
use App\Notifications\RenameTaskNotification;
use App\Notifications\TaskAttachedNotification;
use App\Notifications\TaskSubscribeNotifications;
use App\Notifications\TaskSubscriptionAndAssignmentNotification;
use App\Notifications\TaskWorkflowNotification;
use Carbon\Carbon;

/**
 * Class NotificationResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property array      $data
 * @property bool       $read_at
 * @property Carbon     $created_at
 */
class NotificationResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        return [
            'id'                => $this->id,
            'read_at'           => $this->read_at,
            'created_at'        => (string) $this->created_at,
            'notifiable_id'     => $this->data['id'],
            'sender_id'         => $this->data['sender_id'],
            'sender_name'       => $this->data['sender_name'],
            'sender_avatar'     => !empty($this->data['sender_avatar']) ? $this->data['sender_avatar'] : null,
            'sender_last_name'  => $this->data['sender_last_name'],
            'message'           => $this->data['message'],
            'task_id'           => $this->data['task_id']  ?? null,
            'task_name'         => !empty($this->data['task_name']) ? $this->data['task_name'] : null,
            'board_id'          => $this->data['board_id'] ?? null,
            'group_id'          => $this->data['group_id'] ?? null,
            'action_type'       => $this->getActionType($this->type),
            'notifiable_user'   => $this->notifiable->full_name,
            'created_at_diff_for_humans' => $this->created_at->diffForHumans(),
            'action_message'    => $this->getActionMessage(),
            'type'              => $this->type,
            'data'              => $this->data
        ];
    }

    private function getActionMessage(): string
    {
        switch($this->type)
        {
            case TaskWorkflowNotification::class:
                return trans('alerts.task_closed');
                break;
            case DeadlineNotification::class:
                return trans('alerts.over_deadline');
                break;
            case TaskAttachedNotification::class:
                $message = isset($this->data['message']) ? $this->data['message'] : '';
                $newMessage = preg_match('/unassigned/', $message) ? 'unassigned' : 'assigned';
                return trans($newMessage) . ' ' . $this->checkReceiver();
                break;
            case CommentMentionNotification::class:
                return trans('alerts.mentioned_you');
                break;
            case TaskSubscriptionAndAssignmentNotification::class:
                return trans('alerts.subscribe_and_assign :user', ['user' => $this->checkReceiver()]);
                break;
            case TaskSubscribeNotifications::class:
                if($this->getActionType($this->type) == 'subscribed') {
                    return trans('alerts.user_subscribed', ['user' => $this->checkReceiver()]);
                }
                return trans('alerts.user_unsubscribed', ['user' => $this->checkReceiver()]);
                break;
            case RenameTaskNotification::class:
                return trans('alerts.rename_task');
                break;
            case PrioritizeTaskNotification::class:
                return trans('alerts.changed_priority', ['changes' => $this->data['message']]);
                break;
            case BudgetNotification::class:
                return trans('alerts.over_soft_budget');
                break;
            case ChangeSoftBudgetNotification::class:
                return trans('alerts.soft_budget_changed');
                break;
            case ChangeHardBudgetNotification::class:
                return trans('alerts.hard_budget_changed');
                break;
            case TaskAssignedNotification::class:
                return trans('alerts.task_assigned', ['user' => $this->checkReceiver()]);
                break;
            case TaskCompletedNotification::class:
                return trans('alerts.task_completed');
                break;
            case RenameGroupNotification::class:
                return trans('alerts.rename_group');
                break;
            case RenameBoardNotification::class:
                return trans('alerts.rename_board');
                break;
            case CommentLikeNotification::class:
                return trans('alerts.comment_like');
                break;
            case UnsubscribeFromNotifications::class:
                return trans('alerts.notifications_unsubscribe');
                break;
            case TaskReopenedNotification::class:
                return trans('alerts.task_reopened');
                break;
            default:
                return '';
                break;
        }
    }

    private function getActionType($type)
    {
        if($type === TaskWorkflowNotification::class) {
            return 'done';//done
        }

        if($type === RenameTaskNotification::class) {
            return 'rename';
        }

        if($type === ChangeHardBudgetNotification::class) {
            return 'changed hard budget';
        }

        if($type === ChangeSoftBudgetNotification::class) {
            return 'changed soft budget';
        }

        if($type === CommentMentionNotification::class) {
            return 'mention';
        }

        if($type === TaskAttachedNotification::class) {
            $message = isset($this->data['message']) ? $this->data['message'] : '';
            $newMessage = preg_match('/unassigned/', $message) ? 'unassigned' : 'assigned';
            return $newMessage . ' ' . $this->checkReceiver();
        }

        if($type === TaskSubscribeNotifications::class) {
            $message = isset($this->data['message']) ? $this->data['message'] : '';
            return preg_match('/unsubscribed/', $message) ? 'unsubscribed' : 'subscribed';
        }

        if($type === RenameBoardNotification::class) {
            return 'rename';
        }

        if($type === RenameGroupNotification::class) {
            return 'rename';
        }

        if($type === CommentLikeNotification::class) {
            return 'liked your comment';
        }

        if($type == TaskSubscriptionAndAssignmentNotification::class) {
            return 'subscribed and assigned ' . $this->checkReceiver();
        }

        if($type == PrioritizeTaskNotification::class) {
            return 'changed priority';
        }

        if($type == DeadlineNotification::class) {
            return 'over deadline';
        }

        if($type == BudgetNotification::class) {
            return $this->data['message'];
        }

        return $type;
    }

    /**
     * Method for return current receiver
     * @return mixed|string
     */
    public function checkReceiver()
    {
        $user = trans('alerts.you');
        if(!empty($this->data['receiver_id'])) {
            $user = $this->data['receiver_id'] != \Auth::id() ? $this->data['receiver_name'] : trans('alerts.you');
        }
        return $user;
    }


}
