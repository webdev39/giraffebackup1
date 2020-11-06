<?php

namespace App\Models;

use App\Events\Eloquent\ChangedTaskEvent;
use App\Events\Eloquent\Saved\SavedTaskEvent;
use App\Notifications\PrioritizeTaskNotification;
use App\Notifications\RenameTaskNotification;
use App\Notifications\TaskWorkflowNotification;
use App\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int|null $priority_id
 * @property int|null $creator_id
 * @property int|null $budget_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $deadline
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $done_by
 * @property int|null $open_order
 * @property int|null $done_order
 * @property int $draft
 * @property int $is_archive
 * @property int|null $total_order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Board[] $board
 * @property-read \App\Models\Budget|null $budget
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \App\Models\UserTenant|null $doneBy
 * @property-read mixed $board_detail
 * @property-read mixed $group_detail
 * @property mixed $planned_deadline
 * @property-read mixed $priorities
 * @property-read PersonalDeadline $personalDeadline
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSubscription[] $notificationSubscriptions
 * @property-read \App\Models\Priority|null $priority
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubTask[] $subTasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenant[] $subscribers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Timer[] $timers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDoneBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDoneOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTotalOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereOpenOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificationSubscription[] $notifySubscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenant[] $taskSubscribers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenantTask[] $userTenantTask
 * @property-read null|string $view_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task query()
 * @property int $sort_weight
 * @property-read mixed $icon
 * @property-read mixed $link
 * @property-read \App\Models\Repeat $repeat
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereSortWeight($value)
 * @property int board_id
 * @property int group_id
 */
class Task extends BaseModel implements Taggable
{
    use Searchable, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'budget_id',
        'creator_id',
        'priority_id',
        'repeat_id',
        'deadline',
        'description',
        'done_by',
        'draft',
        'name',
        'sort_weight',
        'created_at',
        'parent_id',
        'board_id',
        'group_id'
    ];

    protected $appends = ['link', 'icon'];

    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName = 'task';

    /**
     * Logging only the changed attributes
     *
     * @var array
     */
    public $logAttributes = [
        'name',
        'description',
        'sort_weight',
        'done_by',
        'priority_id',
        'is_archive',
        'created_at',
        'deadline',
    ];

    /**
     * List of attributes when changing which notification of subscribers
     *
     * @var array
     */
    public $notifyAttributes = [
        'name'          => RenameTaskNotification::class,
        'done_by'       => TaskWorkflowNotification::class,
        'priority_id'   => PrioritizeTaskNotification::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SavedTaskEvent::class,
//        'updated' => ChangedTaskEvent::class,
    ];

    /** @var integer */
    public $sort_order;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        if (!request()->has('isDraftTask') || request()->get('isDraftTask') === false) {
            static::addGlobalScope('draft', function (Builder $builder) {
                $builder->where('draft', '=', false);
            });
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function repeat()
    {
        return $this->hasOne(Repeat::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timers()
    {
        return $this->hasMany(Timer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifySubscriptions()
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTenantTask()
    {
        return $this->hasMany(UserTenantTask::class);
    }

    /**
     * @return null|string
     */
    public function getViewNameAttribute() : ?string
    {
        return optional($this->board->first())->viewName;
    }

    public function getLinkAttribute()
    {
        $board = $this->board->first();
        return '/group/'.$board->group_id.'/board/'.$board->id.'?taskId='.$this->id;
    }


    public function getIconAttribute()
    {
        return 'fa fa-check-square';
    }


    /**
     * @return int|null
     */
    public function getBoardIdAttribute($value) : ?int
    {
        return empty($value) ? optional($this->board->first())->id : $value;
    }

    public function personalDeadline()
    {
        return $this->hasOne(PersonalDeadline::class)
            ->where('user_tenant_id', Auth::userTenantId());
    }

    public function getPlannedDeadlineAttribute()
    {
        $plannedDeadline = null;
        if ($personalDeadline = $this->personalDeadline) {
            $plannedDeadline = $personalDeadline->planned_deadline;
        }

        return $plannedDeadline;
    }
    public function setPlannedDeadlineAttribute($plannedDeadline)
    {
        if ($userTenantId = Auth::userTenantId()) {
            $personalDeadline = PersonalDeadline::where([
                'user_tenant_id' => $userTenantId,
                'task_id'        => $this->id,
            ])->first();

            if ($personalDeadline) {
                $personalDeadline->planned_deadline = $plannedDeadline;
                $personalDeadline->save();
            } else {
                PersonalDeadline::create([
                    'task_id'           => $this->id,
                    'planned_deadline'  => $plannedDeadline,
                    'user_tenant_id'    => $userTenantId,
                ]);
            }
        }
    }

    public function getBoardDetailAttribute()
    {
        if (!$this->relatedBoard) {
            $this->relatedBoard = $this->board()->first();
        }
        return $this->relatedBoard ? [ 'name' => $this->relatedBoard->name, 'boardId' => $this->relatedBoard->id] : null;
    }
    public function getGroupDetailAttribute()
    {
        if (!$this->relatedBoard) {
            $this->relatedBoard = $this->board()->first();
        }
        if ($this->relatedBoard) {
            $group = $this->relatedBoard->group;
            return $group ? ['name' => $group->name, 'groupId' => $group->id] : null;
        }
        return null;
    }

    public function board()
    {
        return $this->belongsToMany(Board::class, (new BoardTask())->getTable(), 'task_id', 'board_id');
    }

    public function userTenant()
    {
        return $this->belongsToMany(UserTenant::class, (new UserTask())->getTable(), 'user_tenant_id', 'task_id')->first();
    }

    public function taskSubscribers()
    {
        return $this->belongsToMany(
            UserTenant::class,
            'user_tenant_task',
            'task_id',
            'user_tenant_id'
        )->withPivot(['id as userTenantTaskId', 'user_tenant_id as userTenantId']);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isCreator(User $user)
    {
        return $this->creator_id == $user->id;
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}