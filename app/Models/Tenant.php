<?php

namespace App\Models;

/**
 * App\Models\Tenant
 *
 * @property int $id
 * @property string|null $company_name
 * @property string|null $project_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Priority[] $customPriorities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $customRoles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pipeline[] $pipelines
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenant[] $userTenants
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant whereProjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tenant query()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscription
 */
class Tenant extends BaseModel
{
    protected $fillable = ['company_name', 'project_name'];
    protected $table = 'tenants';

    public function users()
    {
        return $this->belongsToMany(User::class, (new UserTenant())->getTable(), 'tenant_id', 'user_id');
    }

    public function userTenants()
    {
        return $this->hasMany(UserTenant::class);
    }

    public function owner()
    {
        return $this->users()->wherePivot('is_owner', 1)->first();
    }

    public function customRoles()
    {
        return $this->belongsToMany(Role::class, 'tenant_custom_role', 'tenant_id', 'role_id');
    }

    public function customPriorities()
    {
        return $this->belongsToMany(Priority::class, 'tenant_priority', 'tenant_id', 'priority_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function pipelines()
    {
        return $this->hasMany(Pipeline::class);
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function groups()
    {
        $tenant = $this;
        return Group::with('creator')
            ->whereHas('creator', function($query) use($tenant) {

            });
    }
}
