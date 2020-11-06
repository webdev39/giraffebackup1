<?php

namespace App\Models;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $tenant_id
 * @property string $city
 * @property string $country
 * @property string $email
 * @property string $telephone
 * @property string $street
 * @property string|null $postcode
 * @property string $house
 * @property float $hourly_rate
 * @property string|null $custom_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bill[] $bills
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Board[] $boards
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCustomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereHouse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer status($status = 'active')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereStatus($value)
 * @property string|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer query()
 * @property int|null $country_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCountryId($value)
 */
class Customer extends BaseModel
{
    protected $fillable = [
        'name',
        'status',
        'contact',
        'custom_id',
        'city',
        'email',
        'telephone',
        'street',
        'postcode',
        'house',
        'hourly_rate',
        'tenant_id',
        'country_id',
    ];

    protected $table = 'customers';

    /**
     * Customer status list
     */
    const STATUS = [
        'archived'  => 0,
        'active'    => 1,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function setStatusAttribute(string $value)
    {
        $this->attributes['status'] = self::STATUS[$value];
    }

    /**
     * @param        $query
     * @param string $status
     *
     * @return mixed
     */
    public function scopeStatus($query, string $status = 'active')
    {
        if (array_key_exists($status, self::STATUS)) {
            return $query->where('status', self::STATUS[$status]);
        }
    }
}
