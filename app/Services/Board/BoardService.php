<?php

namespace App\Services\Board;

use App\Collections\BoardCollection;
use App\Collections\TaskCollection;
use App\Collections\TimerCollection;
use App\Models\Board;
use App\Repositories\BoardRepositoryEloquent;
use App\Repositories\TaskRepositoryEloquent;
use App\Services\BaseService;
use App\Services\Timer\TimerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BoardService extends BaseService
{
    /**
     * @var BoardRepositoryEloquent
     */
    public $boardRepo;

    /**
     * @var TaskRepositoryEloquent
     */
    public $taskRepo;

    /**
     * BoardService constructor.
     */
    public function __construct()
    {
        $this->boardRepo = app('BoardRepo');
        $this->taskRepo  = app('TaskRepo');
    }

    /**
     * @param int $boardId
     *
     * @return mixed
     */
    public function getBoardModelById(int $boardId)
    {
        return $this->boardRepo->findOrFail($boardId);
    }

    /**
     * @param Collection $boards
     * @param int|null   $userTenantId
     *
     * @return BoardCollection|Collection
     */
    public function addBoardRelations(Collection $boards, int $userTenantId = null)
    {
        $boardIds    = $boards->pluck('id')->unique()->toArray();

        $tasks       = app('TaskRepo')->getTasksByBoardIds($boardIds, $userTenantId);
        $taskIds     = $tasks->pluck('id')->unique()->toArray();

        $timers      = app('TimerRepo')->getTimersByTaskIds($taskIds);
        $timerIds    = $timers->pluck('id')->unique()->toArray();

        $sortOrder   = app('TaskSortOrderRepo')->getSortOrderByTaskIds($taskIds);
        $pauses      = app('PauseRepo')->getPausesByTimerIds($timerIds);

        $countComments      = app('CommentRepo')->getCountCommentsByTaskIds($taskIds);
        $countAttachments   = app('CommentRepo')->getCountAttachmentsByTaskIds($taskIds);
        $countDoneSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, true);
        $countOpenSubTasks  = app('SubTaskRepo')->getCountSubTasksByTaskIds($taskIds, false);

        $taskSubscribers    = app('UserTenantRepo')->getTaskSubscribersByTaskIds($taskIds);
        $notifySubscribers  = app('UserTenantRepo')->getNotifySubscribersByTaskIds($taskIds);

        $timers = TimerCollection::make($timers);
        $timers->setAttributes([
            'pauses' => $pauses->groupBy('timer_id'),
        ]);

        $tasks = TaskCollection::make($tasks);
        $tasks->setSortOrder($sortOrder->groupBy('task_id'));
        $tasks->setAttributes([
            'timers'                => $timers->groupBy('task_id'),
            'task_subscribers'      => $taskSubscribers->groupBy('task_id'),
            'notify_subscribers'    => $notifySubscribers->groupBy('task_id'),
        ]);

        $tasks->setCountAttributes([
            'comment'               => $countComments,
            'attachment'            => $countAttachments,
            'done_sub_task'         => $countDoneSubTasks,
            'open_sub_task'         => $countOpenSubTasks,
        ]);

        $boards = BoardCollection::make($boards);
        $boards->setAttributes([
            'tasks' => $tasks->groupBy('board_id'),
        ]);

        return $boards;
    }

    /**
     * @param int $boardId
     *
     * @return \stdClass
     */
    public function getBoardById(int $boardId)
    {
        return $this->boardRepo->getBoardsByIds([$boardId])->first();
    }

    /**
     * @param int      $boardId
     * @param int|null $userTenantId
     *
     * @return mixed
     */
    public function getBoardWithRelationsById(int $boardId, int $userTenantId = null)
    {
        $boards = $this->boardRepo->getBoardsByIds([$boardId]);

        return $this->addBoardRelations($boards, $userTenantId)->first();
    }

    /**
     * @param array    $boardIds
     * @param int|null $userTenantId
     *
     * @return BoardCollection|Collection
     */
    public function getBoardsWithRelationsByIds(array $boardIds, int $userTenantId = null)
    {
        $boards = $this->boardRepo->getBoardsByIds($boardIds);

        return $this->addBoardRelations($boards, $userTenantId);
    }

    /**
     * @param array $boardIds
     *
     * @return BoardCollection
     */
    public function getBoardsByIds(array $boardIds)
    {
        return $this->boardRepo->getBoardsByIds($boardIds);
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getBoardsByUserTenantId(int $userTenantId)
    {
        return $this->boardRepo->getBoardsByUserTenantId($userTenantId);
    }

    /**
     * @param int $tenantId
     *
     * @return Collection
     */
    public function getBoardsByTenantId(int $tenantId): Collection
    {
        return $this->boardRepo->getBoardsWithRelationsByTenantId($tenantId);
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getBoardsWithRelationsByUserTenantId(int $userTenantId)
    {
        $boards = $this->boardRepo->getBoardsByUserTenantId($userTenantId);

        return $this->addBoardRelations($boards, $userTenantId);
    }

    /**
     * @param int      $tenantId
     * @param int|null $userTenantId
     *
     * @return BoardCollection|Collection
     */
    public function getBoardsWithRelationsByTenantId(int $tenantId, int $userTenantId = null)
    {
        $boards = $this->boardRepo->getBoardsWithRelationsByTenantId($tenantId);

        return $this->addBoardRelations($boards, $userTenantId);
    }

    /**
     * @param int $userTenantId
     * @param int $lastCount
     *
     * @return Collection
     */
    public function getLatestActiveBoardsByUserTenantId(int $userTenantId, int $lastCount = 5)
    {
        return $this->boardRepo->getLatestActiveBoardsByUserTenantId($userTenantId, $lastCount);
    }

    /**
     * @param      $groupId
     * @param      $boardName
     * @param null $boardId
     *
     * @return bool
     */
    public function hasBoardNameInGroup($groupId, $boardName, $boardId = null) : bool
    {
        /** @var Collection $board */
        $board = $this->boardRepo->findWhere([
            'group_id'  => $groupId,
            'name'      => $boardName,
        ]);

        if ($board->isEmpty()) {
            return false;
        }

        if (!$board->where('id', $boardId)->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * @param array $attributes
     *
     * @return mixed
     * @throws \Throwable
     */
    public function createBoard(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $attributes['budget_id'] = app('BudgetSer')->createOrUpdateBudget($attributes)->id;

            if (!isset($attributes['view_type_id'])) {
                $attributes['view_type_id'] = app('FieldRepo')->getDefaultViewType()->id;
            }

            return $this->boardRepo->create($attributes);
        });
    }

    /**
     * @param array $attributes
     * @param int   $boardId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function updateBoard(array $attributes, int $boardId)
    {
        return DB::transaction(function () use ($attributes, $boardId) {
            app('BudgetSer')->createOrUpdateBudget($attributes, $attributes['budget_id']);

            if(!empty($attributes['deadline']) && $attributes['deadline'] == 'CURRENT_TIMESTAMP') {
                 unset($attributes['deadline']);
            }

            return $this->boardRepo->update($attributes, $boardId);
        });
    }

    /**
     * @param int $boardId
     *
     * @return bool
     * @throws \Throwable
     */
    public function destroyOrArchiveBoard(int $boardId)
    {
        $board      = $this->getBoardWithRelationsById($boardId);
        $taskIds    = $board->tasks->pluck('id')->unique()->toArray();
        $timerIds   = $board->tasks->pluck('timers')->collapse()->pluck('id')->unique()->toArray();

        $loggedTime = TimerService::getTrackedTimeByTasks($board->tasks);

        if ($loggedTime > 0) {
            return $this->archiveBoard($boardId, $taskIds, $timerIds);
        }

        $this->boardRepo->delete($board->id);
        $this->taskRepo->removeTaskByIds($taskIds);

        return true;
    }

    /**
     * @param int   $boardId
     * @param array $taskIds
     * @param array $timerIds
     *
     * @return bool
     * @throws \Throwable
     */
    public function archiveBoard(int $boardId, array $taskIds = [], array $timerIds = [])
    {
        if (!$taskIds || !$timerIds) {
            $board = $this->getBoardWithRelationsById($boardId);

            if (!$taskIds) {
                $taskIds = $board->tasks->pluck('id')->unique()->toArray();
            }

            if (!$timerIds) {
                $timerIds = $board->tasks->pluck('timers')->collapse()->pluck('id')->unique()->toArray();
            }
        }

        DB::transaction(function () use ($boardId, $taskIds, $timerIds) {
            app('TaskSer')->changeIsArchiveTaskByIds($taskIds, true);
            app('TimerSer')->stopTimers($timerIds);

            $this->boardRepo->update(['is_archive' => 1], $boardId);
        });

        return false;
    }

    /**
     * @param int   $boardId
     * @param array $taskIds
     * @param array $timerIds
     *
     * @return bool
     * @throws \Throwable
     */
    public function unarchivedBoard(int $boardId, array $taskIds = [], array $timerIds = [])
    {
        if (!$taskIds || !$timerIds) {
            $board = $this->getBoardWithRelationsById($boardId);

            if (!$taskIds) {
                $taskIds = $board->tasks->pluck('id')->unique()->toArray();
            }

            if (!$timerIds) {
                $timerIds = $board->tasks->pluck('timers')->collapse()->pluck('id')->unique()->toArray();
            }
        }

         DB::transaction(function () use ($boardId, $taskIds, $timerIds) {
            app('TaskSer')->changeIsArchiveTaskByIds($taskIds);
            app('TimerSer')->stopTimers($timerIds);

            $this->boardRepo->update(['is_archive' => 0], $boardId);
        });

        return true;
    }

    /**
     * @param array $boardIds
     * @param bool  $isArchive
     *
     * @throws \Throwable
     */
    public function changeIsArchiveBoardByIds(array $boardIds, bool $isArchive)
    {
        return $this->boardRepo->changeIsArchiveBoardByIds($boardIds, $isArchive);
    }

    /**
     * @param Board $board
     * @param int   $newGroupId
     *
     * @throws \Throwable
     */
    public function moveBoardInGroup(Board $board, int $newGroupId)
    {
        DB::transaction(function () use ($board, $newGroupId) {
            $members = app('GroupSer')->getDiffMembersBetweenGroups($board->group_id, $newGroupId);

            $taskIds        = array_column($board->tasks->toArray(), 'id');
            $userIds        = array_column($members->toArray(), 'user_id');
            $userTenantIds  = array_column($members->toArray(), 'id');

            app('UserTenantTaskRepo')->detachUserTenantFromTasks($taskIds, $userTenantIds);
            app('NotificationSubscriptionRepo')->unsubscribeUserFromTasks($taskIds, $userIds);

            $board->update(['group_id' => $newGroupId]);
        });
    }
}
