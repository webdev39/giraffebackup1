<?php

namespace App\Models;

/**
 * App\Models\UserProfile
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $language_id
 * @property int|null $font_id
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $avatar_id
 * @property int|null $background_id
 * @property string|null $time_zone
 * @property-read \App\Models\Image|null $avatar
 * @property-read \App\Models\Image|null $background
 * @property-read \App\Models\Field|null $font
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereAvatarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereBackgroundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile query()
 */
class UserProfile extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'language_id',
        'font_id',
        'background_id',
        'time_zone',
        'avatar_id',
        'primary_color',
        'secondary_color',
        'selected_color_scheme_id',
        'tour',
        'audio'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    protected $casts = [
        'audio' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function font()
    {
        return $this->belongsTo(Field::class, 'font_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function background()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
