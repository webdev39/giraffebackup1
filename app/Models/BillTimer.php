<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BillTimer
 *
 * @property int $id
 * @property string $time
 * @property string|null $comment
 * @property float $rate
 * @property int $bill_id
 * @property int|null $timer_billing_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $user_id
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\TimerBilling|null $timerBilling
 * @property-read \App\Models\User|null $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BillTimer onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereTimerBillingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BillTimer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BillTimer withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillTimer query()
 */
class BillTimer extends BaseModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'time',
        'comment',
        'rate',
        'unit',
        'bill_id',
        'user_id',
        'timer_billing_id'
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timerBilling()
    {
        return $this->belongsTo(TimerBilling::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
