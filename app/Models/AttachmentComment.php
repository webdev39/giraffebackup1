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
 * @property array|null $spatial
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttachmentComment whereSpatial($value)
 */
class AttachmentComment extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachment_comment';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attachment_id',
        'comment_id',
        'spatial'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'spatial' => 'array',
    ];
}
