<?php

namespace App\Models;

use App\Events\Eloquent\Saved\SavedGroupEvent;
use App\Notifications\RenameGroupNotification;
use App\Taggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

/**
 * App\Models\Group
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 * @property int|null $creator_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Board[] $boards
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenant[] $members
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatorId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenantGroup[] $userTenantGroups
 * @property int $is_archive
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereIsArchive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group query()
 */
class Group extends BaseModel implements Taggable
{
    use Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'is_archive',
        'tenant_id',
    ];

    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName = 'group';

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
        'name' => RenameGroupNotification::class,
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SavedGroupEvent::class,
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTenantGroups()
    {
        return $this->hasMany(UserTenantGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany(
            UserTenant::class,
            (new UserTenantGroup())->getTable(),
            'group_id',
            'user_tenant_id'
        )->withPivot('id as userTenantGroupId');
    }

    public function users()
    {

    }

    /**
     * @param User $user
     * @return UserTenantGroup|null
     */
    public function getUserTenantGroup(User $user)
    {
        return UserTenantGroup::forUserTenantAndGroup($user->getChosenTenant()->id, $this->id)
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
