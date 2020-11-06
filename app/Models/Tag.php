<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class Tag
 * @package App\Models
 * @property string $name
 */
class Tag extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    public function tasks(): MorphToMany
    {
        return $this->morphedByMany(Task::class, 'taggable');
    }

    public function comments(): MorphToMany
    {
        return $this->morphedByMany(Task::class, 'taggable');
    }

    public function boards(): MorphToMany
    {
        return $this->morphedByMany(Board::class, 'taggable');
    }

    public static function createIfNotExists(string $name)
    {
        if(!$tag = static::query()->byName($name)->first()) {
            $tag = Tag::create(['name' => $name]);
        }

        return $tag;
    }

    public function scopeByName(Builder $builder, string $name): Builder
    {
        return $builder->whereName($name);
    }
}
