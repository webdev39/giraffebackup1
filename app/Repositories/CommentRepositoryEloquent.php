<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 26.06.17
 * Time: 17:13
 */

namespace App\Repositories;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Comment::class;
    }

    /**
     * @param bool $skipDescendants
     * @param bool $loadTask
     * @return Builder
     */
    public function buildCommentWithRelations($skipDescendants = true, $loadTask = true)
    {
        $select = [
            $this->commentTable.'.id                    as id',
            $this->commentTable.'.body                  as body',
            $this->commentTable.'.body                  as field',
            $this->commentTable.'.updated_at            as updated_at',
            $this->commentTable.'.created_at            as created_at',
            $this->userTenantTable.'.user_id            as user_id',
            $this->userTenantTable.'.id                 as user_tenant_id',
            $this->taskTable.'.id                       as task_id',
            $this->taskTable.'.name                     as task_name',
            $this->boardTable.'.id                      as board_id',
            $this->boardTable.'.name                    as board_name',
            $this->groupTable.'.id                      as group_id',
            $this->groupTable.'.name                    as group_name',
            $this->commentTable.'.parent_id             as parent_id',
        ];

        $build = DB::table($this->commentTable)
            ->select($select)
            ->join($this->userTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTable.'.id', $this->commentTable.'.user_id');
            })
            ->join($this->userTenantTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTenantTable.'.user_id', $this->userTable.'.id');
                $join->on($this->userTenantTable.'.tenant_id', $this->userTable.'.chosen_tenant_id');
            });

        $build->join($this->taskTable, $this->taskTable.'.id', '=', $this->commentTable.'.task_id', 'left')
            ->join($this->boardTaskTable, $this->boardTaskTable.'.task_id', '=', $this->taskTable.'.id', 'left')
            ->join($this->boardTable, $this->boardTable.'.id', '=', $this->commentTable.'.board_id', 'left')
            ->join($this->groupTable, $this->groupTable.'.id', '=', $this->commentTable.'.groupId', 'left');

        if ($skipDescendants) {
            $build->whereNull($this->commentTable.'.parent_id');
        }

        return $build;
    }

    /**
     * @param array $commentIds
     * @param bool $skipDescendants
     * @param bool $loadTask
     * @return Collection
     */
    public function getCommentByIds(array $commentIds, $skipDescendants = true, $loadTask = true) : Collection
    {
        return $this->buildCommentWithRelations($skipDescendants, $loadTask)
            ->whereIn($this->commentTable.'.id', $commentIds)
            ->get();
    }

    /**
     * @param int      $taskId
     * @param int|null $userTenantId
     *
     * @return Collection
     */
    public function getCommentsByTaskId(int $taskId, int $userTenantId = null)
    {
        $build = $this->buildCommentWithRelations()
            ->where($this->taskTable.'.id', $taskId);

        if (!is_null($userTenantId)) {
            return $build->where($this->userTenantTable . '.id', $userTenantId)->get();
        }

        return $build->get();
    }

    /**
     * @param int $attachmentId
     *
     * @return Collection
     */
    public function getAttachmentCommentsByAttachmentId(int $attachmentId)
    {
        return $this->buildCommentWithRelations()
            ->addSelect([
                $this->attachmentCommentTable.'.spatial as spatial'
            ])
            ->join($this->attachmentCommentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->attachmentCommentTable.'.comment_id', $this->commentTable.'.id');
            })
            ->where($this->attachmentCommentTable.'.attachment_id', $attachmentId)
            ->get();
    }

    /**
     * @param int $commentId
     *
     * @return Collection
     */
    public function getAttachmentCommentById(int $commentId)
    {
        return $this->buildCommentWithRelations()
            ->addSelect([
                $this->attachmentCommentTable.'.spatial as spatial'
            ])
            ->join($this->attachmentCommentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->attachmentCommentTable.'.comment_id', $this->commentTable.'.id');
            })
            ->where($this->commentTable.'.id', $commentId)
            ->get();
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getCountCommentsByTaskIds(array $taskIds)
    {
        return DB::table($this->commentTable)
            ->select([
                $this->commentTable.'.task_id',
                DB::raw('count(id) as total')
            ])
            ->whereIn($this->commentTable.'.task_id', $taskIds)
            ->groupBy($this->commentTable.'.task_id')
            ->get();
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getCountAttachmentsByTaskIds(array $taskIds)
    {
        return DB::table($this->commentTable)
            ->select([
                $this->commentTable.'.task_id',
                DB::raw('count('.$this->commentAttachmentTable.'.id) as total')
            ])
            ->join($this->commentAttachmentTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->commentAttachmentTable.'.comment_id', $this->commentTable.'.id');
            })
            ->whereIn($this->commentTable.'.task_id', $taskIds)
            ->groupBy($this->commentTable.'.task_id')
            ->get();
    }

    /**
     * @param array $commentIds
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDescendantsAndSelf(array $commentIds)
    {
        return $this->model->with('descendants')->findMany($commentIds);
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $filters
     * @param array $range
     * @param array $users
     * @param array $assigned
     * @param array $createdBy
     * @param array $subscribed
     * @param bool|null $close
     * @return Builder
     */
    public function getFilteredComments(
        string $table,
        int $id,
        array $filters,
        array $range,
        array $users,
        array $assigned,
        array $createdBy,
        array $subscribed,
        bool $close = null
    ): Builder
    {
        $comments = $this->buildCommentWithRelations()
            ->addSelect([
                DB::raw("'comment' as source"),
            ])
            ->leftJoin($this->attachmentCommentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->attachmentCommentTable . '.comment_id', $this->commentTable . '.id');
            })
            ->whereNull($this->attachmentCommentTable . '.id');
        if($table != 'boards') {
            $comments->where($table . '.id', $id);
        } else {
            $comments->where($this->boardTable . '.id', $id);
        }

        if (in_array('files', $filters, false) && !in_array('comments', $filters, false)) {
            $comments = $comments->leftJoin($this->commentAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->commentAttachmentTable . '.comment_id', $this->commentTable . '.id');
            })->whereNotNull($this->commentAttachmentTable . '.comment_id');
        }

        if (count($range) === 2) {
            $comments = $comments->whereBetween($this->commentTable . '.created_at', [
                Carbon::parse($range[0]),
                Carbon::parse($range[1])
            ]);
        }

        if (count($users) > 0) {
            $comments = $comments->whereIn($this->userTenantTable . '.user_id', $users);
        }

        if (count($assigned) > 0) {
            $comments = $comments
                ->leftJoin($this->userTenantTaskTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->userTenantTaskTable . '.task_id', $this->taskTable . '.id');
                })
                ->whereIn($this->userTenantTaskTable . '.user_tenant_id', $assigned);
        }

        if (count($createdBy) > 0) {
            $comments = $comments->whereIn($this->commentTable. '.user_id', $createdBy);
        }

        if (count($subscribed) > 0) {
            $comments = $comments
                ->join($this->notificationSubscriptionTable, function ($join) {
                    /** @var JoinClause $join */
                    $join->on($this->notificationSubscriptionTable . '.task_id', $this->taskTable . '.id');
                })
                ->whereIn($this->notificationSubscriptionTable . '.user_id', $subscribed);
        }

        if (!is_null($close)) {
            $operator = $close ? '!=' : '=';
            $comments = $comments->where($this->taskTable . '.done_by', $operator, null);
        }

        return $comments;
    }
}
