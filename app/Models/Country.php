<?php

namespace App\Models;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $full_name
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string $iso_3166_2
 * @property string $iso_3166_3
 * @property string $country_code
 * @property string|null $currency_code
 * @property string|null $calling_code
 * @property int $eea
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCallingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereEea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereIso31662($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereIso31663($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @property string $alias
 * @property string $code
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereUpdatedAt($value)
 */
class Country extends BaseModel
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
        'calling_code',
        'currency_code',
        'country_code',
        'iso_3166_2',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'full_name',
        'capital',
        'citizenship',
        'country_code',
        'iso_3166_3',
        'eea',
    ];
}
