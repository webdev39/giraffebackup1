<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\Pipeline
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $is_active
 * @property string $name
 * @property string|null $description
 * @property string $host
 * @property int $port
 * @property string $encryption
 * @property string $email
 * @property mixed $password
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PipelineRule[] $rules
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereEncryption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pipeline query()
 */
class Pipeline extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pipelines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'is_active',
        'name',
        'description',
        'email',
        'host',
        'port',
        'encryption',
        'email',
        'password'
    ];

    /**
     * Get the decrypted password.
     *
     * @return mixed
     */
    public function getPasswordAttribute()
    {
        return Crypt::decrypt($this->attributes['password']);
    }

    /**
     * Save encrypted password.
     *
     * @param  $value
     *
     * @return void
     * @todo   Use a different approach to save the password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encrypt($value);
    }

    /**
     * Get the associated tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the rules for the pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules()
    {
        return $this->hasMany(PipelineRule::class);
    }
    
    public function deactivate()
    {
        $this->is_active = 0;
        $this->save();
    }
}
