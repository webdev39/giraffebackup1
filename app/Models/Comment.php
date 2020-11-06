<?php

namespace App\Models;

use App\Events\Eloquent\Saved\SavedCommentEvent;
use App\Taggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Scout\Searchable;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property string|null $body
 * @property int $is_edited
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read mixed $can_delete
 * @property-read mixed $can_update
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIsEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserId($value)
 * @mixin \Eloquent
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property int $groupId
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment query()
 */
class Comment extends BaseModel implements Taggable
{
    use NodeTrait;

    use Searchable {
        Searchable::usesSoftDelete insteadof NodeTrait;
    }

    /** @var  Task */
    protected $task;

    /** @var  UserTenant */
    protected $userTenant;

    /** @var  UserTenantGroup */
    protected $userTenantGroup;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'body',
        'is_edited',
        'groupId',
        'board_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'canUpdate',
        'canDelete'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
//        'saved' => SavedCommentEvent::class,
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'    => $this->id,
            'body'  => $this->body,
        ];
    }

    private function handlerAttribute(String $permission)
    {
        if (!$this->task) {
            $this->task = $this->task()->first() ?? Task::find(request()->taskId);
        }

        if(!Auth::id()) {
            return false;
        }

        if (!$this->userTenant) {
            $this->userTenant = Auth::userTenant();
        }

        if ($this->user_id == $this->userTenant->user_id) {
            return true;
        }

        if (!$this->userTenantGroup) {
            $this->userTenantGroup = Auth::userTenantGroup($this->task->groupDetail['groupId'] ?? null);
        }

        return $this->userTenant->can($permission) || $this->userTenantGroup->can($permission);
    }

    public function getCanUpdateAttribute()
    {
        return $this->handlerAttribute(Permission::EDIT_TIME_LOGS_PERMISSION['name']);
    }

    public function getCanDeleteAttribute()
    {
        return $this->handlerAttribute(Permission::DELETE_TIME_LOGS_PERMISSION['name']);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'comment_attachment', 'comment_id', 'attachment_id');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isCreator(User $user)
    {
        return $this->user_id == $user->id;
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function reactions()
    {
        return $this->morphMany(Reactions::class, 'targetable');
    }

    public function likes()
    {
        return $this->reactions()->whereReaction(Reactions::LIKE);
    }

    public function sticks()
    {
        return $this->reactions()->whereReaction(Reactions::STICK);
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_tenant_id', $user->user_tenant->id)->exists();
    }

    public function isStickedBy(User $user): bool
    {
        return $this->sticks()->where('user_tenant_id', $user->user_tenant->id)->exists();
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'groupId');
    }
}
