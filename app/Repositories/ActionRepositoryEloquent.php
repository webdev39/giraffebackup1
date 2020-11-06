<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\UserTenant;
use App\Services\Comment\CommentService;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * Class ActivityLogRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActionRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * @var ActivityLogRepositoryEloquent
     */
    private $activityLogRepositoryEloquent;
    /**
     * @var CommentRepositoryEloquent
     */
    private $commentRepositoryEloquent;
    /**
     * @var TimerLogRepositoryEloquent
     */
    private $timerLogRepositoryEloquent;
    /**
     * @var CommentService
     */
    private $commentService;

    public function __construct(
        Application $app,
        ActivityLogRepositoryEloquent $activityLogRepositoryEloquent,
        CommentRepositoryEloquent $commentRepositoryEloquent,
        TimerLogRepositoryEloquent $timerLogRepositoryEloquent,
        CommentService $commentService
    ) {
        parent::__construct($app);
        $this->activityLogRepositoryEloquent = $activityLogRepositoryEloquent;
        $this->commentRepositoryEloquent = $commentRepositoryEloquent;
        $this->timerLogRepositoryEloquent = $timerLogRepositoryEloquent;
        $this->commentService = $commentService;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Activity::class;
    }

    /**
     * @param array  $options
     * @param string $table
     * @param int    $id
     *
     * @return Builder
     */
    private function buildAction(array $options, string $table, int $id): Builder
    {
        $filters    = $options['filters']    ?? [];
        $columns    = $options['columns']    ?? [];
        $range      = $options['range']      ?? [];
        $users      = $options['users']      ?? [];
        $assigned   = $options['assigned']   ?? [];
        $createdBy  = $options['created_by'] ?? [];
        $subscribed = $options['subscribed'] ?? [];
        $close      = $options['close']      ?? null;
        $other      = null;

        $other = null;

        if(!is_null($createdBy)) {
            $createdBy = UserTenant::whereIn('id', $createdBy)->pluck('user_id')->toArray();
        }

        $activityLogs = $this->activityLogRepositoryEloquent->getFilteredActivityLogs($table, $id, $filters, $columns, $range, $users, $assigned, $createdBy, $subscribed, $other, $close);
        $comments = $this->commentRepositoryEloquent->getFilteredComments($table, $id, $filters, $range, $users, $assigned, $createdBy, $subscribed, $close);
        $timerLogs = $this->timerLogRepositoryEloquent->getFilteredTimerLogs($table, $id, $filters, $range, $users, $assigned, $createdBy, $subscribed, $close);

        if (in_array('activity_logs', $filters, false) || count(array_intersect($filters, ActivityLogRepositoryEloquent::ACTIVITY_LOG_FILTERS))) {
            $query = $activityLogs;
        }

        if (in_array('comments', $filters, false) || in_array('files', $filters, false)) {
            $query = isset($query) ? $query->unionAll($comments) : $comments;
        }

        if (in_array('timer_logs', $filters, false)) {
            $query = isset($query) ? $query->unionAll($timerLogs) : $timerLogs;
        }

        if (!isset($query)) {
            $query = $activityLogs->unionAll($comments)->unionAll($timerLogs);
        }
        return $query->orderByDesc('created_at');
    }

    /**
     * @param int   $taskId
     * @param array $options
     * @param int   $page
     * @param int   $perPage
     *
     * @return Collection
     */
    public function getActionByTaskId(int $taskId, array $options, $page = 1, $perPage = 30): Collection
    {
        return $this->buildAction($options, $this->taskTable, $taskId)->forPage($page, $perPage)->get();
    }

    /**
     * @param int   $boardId
     * @param array $options
     * @param int   $page
     * @param int   $perPage
     *
     * @return Collection
     */
    public function getActionByBoardId(int $boardId, array $options, $page = 1, $perPage = 30): Collection
    {
        $actions = $this->buildAction($options, $this->boardTable, $boardId)->forPage($page, $perPage)->get();;
        return $actions;
    }

    /**
     * @param int $groupId
     * @param array $options
     * @param int $page
     * @param int $perPage
     * @return Collection
     * @throws \Exception
     */
    public function getActionByGroupId(int $groupId, array $options, $page = 1, $perPage = 30): Collection
    {
        $actions = $this->buildAction($options, $this->groupTable, $groupId)->forPage($page, $perPage)->get();
        foreach ($actions as &$action) {
            if($action->source == 'comment') {
                $comment = Comment::find($action->id);
                if(!$comment->task_id) {
                    $action->task_id = null;
                    $action->task_title = null;
                    $action->task_name = null;
                    $action->board_id = null;
                    $action->group_id = $comment->groupId;
                }
                $action->replies = $this->commentService->getLastReplies($action->id, 3);
            }
            if(!empty($action->task_id)) {
                $action->task_id = (int) $action->task_id;
            }
        }

        return $actions;
    }

}
