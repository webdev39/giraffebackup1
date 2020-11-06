<?php

namespace App\Models;

use App\Events\Eloquent\Saved\SavedBoardEvent;
use App\Notifications\RenameBoardNotification;
use App\Taggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Auth;
/**
 * App\Models\Board
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $group_id
 * @property string $name
 * @property string $description
 * @property string $deadline
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereUpdatedAt($value)
 * @property string|null $deleted_at
 * @property int $view_type_id
 * @property int|null $budget_id
 * @property int|null $customer_id
 * @property int $is_archive
 * @property-read \App\Models\Budget|null $budget
 * @property-read \App\Models\Budget|null $customer
 * @property-read \App\Models\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\ViewType $viewType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PipelineRule[] $pipelineRules
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereBudgetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereViewTypeId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $doneTasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $openTasks
 * @property int|null $priority_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $archivedTasks
 * @property-read \App\Models\Priority|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board wherePriorityId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Priority[] $customPriorities
 * @property-read string $view_name
 * @property int|null $hide_done_tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Board whereHideDoneTasks($value)
 * @property-read mixed $icon
 * @property-read mixed $link
 */
class Board extends BaseModel implements Taggable
{
    use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'boards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'budget_id',
        'customer_id',
        'deadline',
        'deleted_at',
        'description',
        'group_id',
        'is_archive',
        'name',
        'priority_id',
        'view_type_id',
        'hide_done_tasks',
        'creator_id',
    ];

    protected $appends = ['link', 'icon'];

    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName = 'board';

    /**
     * Logging only the changed attributes
     *
     * @var array
     */
    public $logAttributes = ['name'];

    /**
     * List of attributes when changing which notification of subscribers
     *
     * @var array
     */
    public $notifyAttributes = [
        'name' => RenameBoardNotification::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SavedBoardEvent::class,
    ];

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
     * @return string
     */
    public function getViewNameAttribute()
    {
        return $this->viewType->name;
    }

    public function getLinkAttribute()
    {
        return '/group/'.$this->group->id.'/board/'.$this->id;
    }

    public function getIconAttribute()
    {
        return 'fa fa-times';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function viewType()
    {
        return $this->belongsTo(Field::class, 'view_type_id');
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
    public function customer()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }







    public function tasks()
    {
        return $this->belongsToMany(Task::class, (new BoardTask())->getTable(), 'board_id', 'task_id')
            ->where('is_archive',0);
    }
    public function activeTasks()
    {
        return $this->belongsToMany(Task::class, (new BoardTask())->getTable(), 'board_id', 'task_id')
            ->whereNull('tasks.done_by')
            ->with('timers', 'timers.pauses', 'personalDeadline');
    }

    public function archivedTasks()
    {
        return $this->belongsToMany(Task::class, (new BoardTask())->getTable(), 'board_id', 'task_id')
            ->where('is_archive',1);
    }

    public function openTasks()
    {
        return $this->belongsToMany(Task::class, (new BoardTask())->getTable(), 'board_id', 'task_id')->whereNull('done_by')->where('is_archive',0);
    }

    public function doneTasks()
    {
        return $this->belongsToMany(Task::class, (new BoardTask())->getTable(), 'board_id', 'task_id')->where('done_by', '!=',null)->where('is_archive',0);
    }

    public function customPriorities()
    {
        return $this->belongsToMany(
            Priority::class,
            (new BoardPriority())->getTable(),
            'board_id',
            'priority_id'
        );
    }
    /**
     * Get the associated rules of a pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pipelineRules()
    {
        return $this->belongsToMany(PipelineRule::class, 'pipeline_rule_board');
    }

    /**
     * @param User $user
     * @return UserTenantGroup|null
     */
    public function getUserTenantGroup(User $user)
    {
        return UserTenantGroup::forUserTenantAndGroup($user->getChosenTenant()->id, $this->group_id)
            ->first();
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
