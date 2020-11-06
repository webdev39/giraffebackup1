<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LogAttachment
 *
 * @property int $id
 * @property int $log_id
 * @property int $attachment_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment whereAttachmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment whereLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogAttachment query()
 */
class LogAttachment extends BaseModel
{
    protected $table = 'log_attachment';
    protected $fillable = ['log_id', 'attachment_id'];
}
