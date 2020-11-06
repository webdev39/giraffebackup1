<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Bill
 *
 * @property int $id
 * @property int $customer_id
 * @property int $tenant_id
 * @property int $invoice_number
 * @property int $status
 * @property float|null $time
 * @property float|null $rate
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property float|null $amount
 * @property-read \App\Models\BillLayout $billLayout
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillTimer[] $billTimers
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TimerBilling[] $timerBillings
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bill onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bill withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bill withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bill query()
 */
class Bill extends BaseModel
{
    use SoftDeletes;
    
    const DEFAULT_STATUS = self::STATUS_PAID;
    const STATUS_DRAFT = 'draft';
    const STATUS_FINISHED = 'finished';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    
    const STATUSES = [self::STATUS_DRAFT, self::STATUS_FINISHED, self::STATUS_PAID, self::STATUS_CANCELLED];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'customer_id',
        'time',
        'rate',
        'amount',
        'invoice_number',
        'status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billTimers()
    {
        return $this->hasMany(BillTimer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function timerBillings()
    {
        return $this->hasManyThrough(TimerBilling::class,BillTimer::class, 'bill_id', 'id', 'id', 'timer_billing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billLayout()
    {
        return $this->hasOne(BillLayout::class);
    }
    
    public function logs(): HasMany
    {
        return $this->hasMany(BillLog::class);
    }
}
