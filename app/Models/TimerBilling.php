<?php

namespace App\Models;

/**
 * App\Models\TimerBilling
 *
 * @property int $id
 * @property int $timer_id
 * @property int $billing_status_id
 * @property int $rate
 * @property string $time_bill
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\BillTimer $billTimer
 * @property-read \App\Models\BillingStatus $status
 * @property-read \App\Models\Timer $timer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereBillingStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereTimeBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereTimerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimerBilling query()
 */
class TimerBilling extends BaseModel
{
    const DEFAULT_RATE = 0;
    const DEFAULT_STATUS = 0;
    /**
     * @var array
     */
    protected $fillable = ['timer_id', 'billing_status_id', 'rate','time_bill'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(BillingStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timer()
    {
        return $this->belongsTo(Timer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billTimer()
    {
        return $this->hasOne(BillTimer::class);
    }
}
