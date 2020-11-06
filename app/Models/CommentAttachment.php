<?php

namespace App\Models;

/**
 * App\Models\CommentAttachment
 *
 * @property int $id
 * @property int|null $attachment_id
 * @property int|null $comment_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment whereAttachmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CommentAttachment query()
 */
class CommentAttachment extends BaseModel
{
    protected $fillable = [
        'comment_id',
        'attachment_id'
    ];

    protected $table = 'comment_attachment';
}
