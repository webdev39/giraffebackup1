<?php

namespace App\Models;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string $iso_639_1
 * @property int $is_local
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereIsLocal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereIso6391($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language query()
 * @property string $alias
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereUpdatedAt($value)
 */
class Language extends BaseModel
{
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
        'name',
        'iso_639-1',
        'is_local'
    ];

    /**
     * @return Language|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getDefaultLanguage()
    {
        return (new self())->where('iso_639_1', 'en')->first();
    }
}
