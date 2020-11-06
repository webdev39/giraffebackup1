<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 26.06.17
 * Time: 17:13
 */

namespace App\Repositories;

use App\Models\CommentAttachment;

class CommentAttachmentRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CommentAttachment::class;
    }

}
