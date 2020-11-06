<?php

namespace App\Models;

/**
 * App\Models\BillLayoutType
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BillLayout[] $billLayouts
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BillLayoutType query()
 */
class BillLayoutType extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'alias', 'description'
    ];

    const BILL_LAYOUT_TYPES = [
        'Short' => [
            'id' => 1,
            'name' => 'short',
            'alias' => 'Short Form',
            'description' => 'Short PDF form'
        ],

        'Long' => [
            'id' => 2,
            'name' => 'long',
            'alias' => 'Long Form',
            'description' => 'Long PDF form'
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billLayouts()
    {
        return $this->hasMany(BillLayout::class);
    }
}
