<?php

namespace App\Models;

/**
 * App\Models\BillLayout
 *
 * @property int $id
 * @property string $path
 * @property string $bill_date
 * @property int $bill_layout_type_id
 * @property int $bill_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\BillLayoutType $billLayoutType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereBillDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereBillLayoutTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayout query()
 */
class BillLayout extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'bill_date',
        'bill_layout_type_id',
        'bill_id',
        'created_at'
    ];

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
    public function billLayoutType()
    {
        return $this->belongsTo(BillLayoutType::class);
    }
}
