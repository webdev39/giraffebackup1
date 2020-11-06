<?php

namespace App\Repositories;

use App\Models\AttachmentComment;

class AttachmentCommentRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * @return string
     */
    public function model()
    {
        return AttachmentComment::class;
    }
}
