<?php

namespace App\Models;

/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property string $path
 * @property int $is_image
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $name
 * @property int|null $creator_id
 * @property int|null $size
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereIsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attachment query()
 */
class Attachment extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'name',
        'size',
        'is_image',
        'creator_id',
        'comment',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class, 'comment_attachment');
    }
    
    public function getPathToOriginalAttribute()
    {
        return str_replace('-thumb', '', $this->path);
    }
}