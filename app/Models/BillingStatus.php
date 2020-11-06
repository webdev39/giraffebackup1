<?php

namespace App\Models;

/**
 * App\Models\BillingStatus
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $color
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TimerBilling[] $billings
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillingStatus query()
 */
class BillingStatus extends BaseModel
{
    /**
     * @var array
     */
    const INITIAL_STATUSES = [
        'Open' => [
            'id' => 1,
            'name' => 'Unbilled',
            'alias' => 'Open',
            'color' => 'red'
        ],
        'Unknown' => [
            'id' => 2,
            'name' => 'Not Billable',
            'alias' => 'Not billable',
            'color' => 'yellow'
        ],
        'Parked' => [
            'id' => 3,
            'name' => 'Parked',
            'alias' => 'Parked',
            'color' => 'blue'
        ],
        'Billed' => [
            'id' => 4,
            'name' => 'Billed',
            'alias' => 'Billed',
            'color' => 'lightgreen'
        ]
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'billing_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'alias',
        'color'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billings()
    {
        return $this->hasMany(TimerBilling::class);
    }
}
