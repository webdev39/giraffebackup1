<?php

namespace App\Models;

/**
 * App\Models\UserTenantSettings
 *
 * @property int $id
 * @property int $user_tenant_id
 * @property int|null $logo_id
 * @property int|null $font_id
 * @property array|null $bill_settings
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $web
 * @property int|null $postcode
 * @property string|null $city
 * @property string|null $street
 * @property int|null $currency_id
 * @property int|null $font_size
 * @property int|null $fee
 * @property string|null $creator
 * @property string|null $author
 * @property string|null $title
 * @property string|null $subject
 * @property string|null $keywords
 * @property string|null $filename
 * @property string|null $date_format
 * @property array|null $money_format
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency|null $currency
 * @property-read \App\Models\Field|null $font
 * @property-read \App\Models\Image|null $logo
 * @property-read \App\Models\UserTenant $user_tenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereDateFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereLogoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereMoneyFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings whereUserTenantId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserTenantSettings query()
 */
class UserTenantSettings extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_tenant_id',
        'currency_id',
        'logo_id',
        'bill_settings',
        'email',
        'phone',
        'web',
        'postcode',
        'city',
        'street',
        'font_id',
        'fee',
        'creator',
        'author',
        'title',
        'subject',
        'keywords',
        'filename',
        'date_format',
        'money_format',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'money_format' => 'array',
    ];

    /**
     * Relations
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo()
    {
        return $this->belongsTo(Image::class, 'logo_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_tenant()
    {
        return $this->belongsTo(UserTenant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function font()
    {
        return $this->belongsTo(Field::class, 'font_id');
    }
}
