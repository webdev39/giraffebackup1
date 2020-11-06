<?php

namespace App\Repositories;

use App\Models\Attachment;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttachmentRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Attachment::class;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function buildAttachmentWithRelations()
    {
        return DB::table($this->attachmentTable)
            ->select([
                $this->attachmentTable.'.*',
            ]);
    }

    /**
     * @param array $logIds
     *
     * @return Collection
     */
    public function getAttachmentIdsByLogIds(array $logIds) : Collection
    {
        return $this->buildAttachmentWithRelations()
            ->addSelect([
                $this->logAttachmentTable.'.log_id as log_id',
            ])
            ->leftJoin($this->logAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->logAttachmentTable.'.attachment_id', $this->attachmentTable.'.id');
            })
            ->whereIn($this->logAttachmentTable.'.log_id', $logIds)
            ->get();
    }

    /**
     * @param array $commentIds
     *
     * @return Collection
     */
    public function getAttachmentIdsByCommentIds(array $commentIds) : Collection
    {
        return $this->buildAttachmentWithRelations()
            ->addSelect([
                $this->commentAttachmentTable.'.comment_id as comment_id',
            ])
            ->leftJoin($this->commentAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->commentAttachmentTable.'.attachment_id', $this->attachmentTable.'.id');
            })
            ->whereIn($this->commentAttachmentTable.'.comment_id', $commentIds)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getUnusedAttachments()
    {
        return $this->buildAttachmentWithRelations()
            ->addSelect([
                $this->attachmentTable.'.*',
            ])
            ->leftJoin($this->commentAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->commentAttachmentTable.'.attachment_id', $this->attachmentTable.'.id');
            })
            ->leftJoin($this->logAttachmentTable, function ($join) {
                /** @var JoinClause $join */
                $join->on($this->logAttachmentTable.'.attachment_id', $this->attachmentTable.'.id');
            })
            ->whereNull($this->commentAttachmentTable.'.id')
            ->whereNull($this->logAttachmentTable.'.id')
            ->get();
    }
}
