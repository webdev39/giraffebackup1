<?php

namespace App\Services\Tenant;

use App\Collections\UserTenantCollection;
use App\Collections\UserTenantGroupCollection;
use App\Models\Board;
use App\Models\Currency;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenant;
use App\Models\UserTenantSettings;
use App\Models\UserTenantTemplates;
use App\Repositories\UserTenantSettingsRepositoryEloquent;
use App\Repositories\UserTenantTemplatesRepositoryEloquent;
use App\Services\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class TenantService
 *
 * @package App\Services\Tenant
 */
class TenantService extends BaseService
{
    /** @var \App\Repositories\TenantRepositoryEloquent */
    private $tenantRepo;

    /** @var \App\Repositories\UserTenantRepositoryEloquent */
    private $userTenantRepo;

    /** @var \Illuminate\Foundation\Application|mixed|UserTenantSettingsRepositoryEloquent */
    private $userTenantSettingsRepo;

    /** @var \Illuminate\Foundation\Application|mixed|UserTenantTemplatesRepositoryEloquent */
    private $userTenantTemplatesRepo;
    /**
     * @var \App\Services\Group\GroupService|\Illuminate\Foundation\Application
     */
    private $groupService;
    private $boardService;
    private $priorityService;

    /**
     * TenantService constructor.
     */
    public function __construct()
    {
        $this->tenantRepo               = app('TenantRepo');
        $this->userTenantRepo           = app('UserTenantRepo');
        $this->userTenantSettingsRepo   = app('UserTenantSettingsRepo');
        $this->userTenantTemplatesRepo  = app('UserTenantTemplatesRepo');
        $this->groupService             = app('GroupSer');
        $this->boardService             = app('BoardSer');
        $this->priorityService          = app('PrioritySer');
    }

    /**
     * @param string     $key
     * @param UserTenant $userTenant
     *
     * @return string
     */
    public static function getCacheKey(string $key, UserTenant $userTenant): string
    {
        return "company.{$key}.{$userTenant->tenant_id}";
    }

    /**
     * @return array
     */
    public function getDefaultSettings(): array
    {
        return Cache::remember('company_settings', 360, function () {
            $default  = config("company.settings");

            if (is_null($default['currency_id'])) {
                $default['currency_id'] = Currency::where('code', 'EUR')->first(['id'])->id;
            }

            if (is_null($default['font_id'])) {
                $default['font_id'] = app('FieldRepo')->getDefaultFount()->id;
            }

            return $default;
        });
    }

    /**
     * @param UserTenant $userTenant
     *
     * @return UserTenantSettings
     */
    public function getCompanySettingsByUserTenant(UserTenant $userTenant)
    {
        return Cache::rememberForever(self::getCacheKey('settings', $userTenant), function () use ($userTenant) {
            $default  = $this->getDefaultSettings();
            $settings = $this->userTenantSettingsRepo->getCompanySettingsByTenantId($userTenant->tenant_id);

            if (empty($settings)) {
                return UserTenantSettings::createFromStd((object) $default);
            }

            $settings = $settings->toArray();
            if (isset($settings['bill_settings'])) {
                $settings['bill_settings'] = json_decode($settings['bill_settings']);
            }

            foreach ($settings as $key => $value) {
                $settings[$key] = $value ?? $default[$key];
            }

            return UserTenantSettings::createFromStd((object) $settings);
        });
    }

    /**
     * @param array      $attributes
     * @param UserTenant $userTenant
     *
     * @return int|null
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCompanySettings(array $attributes, UserTenant $userTenant)
    {
        $settings = $this->userTenantSettingsRepo->getCompanySettingsByTenantId($userTenant->tenant_id);
        $default = $this->getDefaultSettings();

        if (isset($attributes['bill_settings'])) {
            $attributes['bill_settings'] = json_encode($attributes['bill_settings']);
        }

        foreach ($attributes as $key => $attribute) {
            if (isset($default[$key]) && $attribute == $default[$key]) {
                unset($attributes[$key]);
            }
        }

        if ($settings) {
            foreach ($attributes as $key => $attribute) {
                if (isset($settings[$key]) && $attribute == $settings->$key) {
                    unset($attributes[$key]);
                }
            }

            if ($settings->currency_id && !isset($attributes['currency_id'])) {
                $attributes['currency_id'] = null;
            }

            if ($settings->font_id && !isset($attributes['font_id'])) {
                $attributes['font_id'] = null;
            }
        }

        if (empty($attributes)) {
            return null;
        }

        if (isset($attributes['logo'])) {
            $attributes['logo_id'] = $attributes['logo'] == 'remove' ? null : app('ImageSer')->uploadImage($attributes['logo'], $userTenant->user_id);

            unset($attributes['logo']);
        }

        $attributes['user_tenant_id'] = $userTenant->id;

        Cache::forget(self::getCacheKey('settings', $userTenant));
        
        return $this->userTenantSettingsRepo->updateCompanySettings($attributes, $userTenant->tenant_id);
    }

    /**
     * @param UserTenant $userTenant
     *
     * @return Collection|\IlluminateAgnostic\Arr\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\Vanilla\Support\Collection
     * @throws \Throwable
     */
    public function getCompanyTemplates(UserTenant $userTenant)
    {
        return Cache::rememberForever(self::getCacheKey('templates', $userTenant), function () use ($userTenant) {
            $templates = $this->userTenantTemplatesRepo->getCompanyTemplatesByTenantId($userTenant->tenant_id);

            $defaults = config("company.templates");

            foreach ($defaults as $type => $group) {
                foreach ($group as $key => $view) {
                    $defaults[$type][$key] = $templates->where('type', $type)->where('key', $key)->first();

                    if ($defaults[$type][$key] instanceof UserTenantTemplates) {
                        $defaults[$type][$key] = $defaults[$type][$key]->content;
                    } else {
                        $defaults[$type][$key] = view($view)->render();
                    }
                }
            }

            return collect($defaults);
        });
    }

    /**
     * @param string     $type
     * @param string     $key
     * @param string     $content
     * @param UserTenant $userTenant
     *
     * @return mixed|null
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function updateCompanyTemplate(string $type, string $key, string $content, UserTenant $userTenant)
    {
        if (view(config("company.templates.{$type}.{$key}"))->render() == $content) {
            return null;
        }

        Cache::forget(self::getCacheKey('templates', $userTenant));

        return $this->userTenantTemplatesRepo->updateCompanyTemplate($type, $key, $content, $userTenant->tenant_id);
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getUserTenantById(int $userTenantId)
    {
        return $this->userTenantRepo->findOrFail($userTenantId);
    }

    /**
     * @param int $userId
     *
     * @return Collection
     */
    public function getUserTenantByUserId(int $userId) : Collection
    {
        return $this->userTenantRepo->findWhere(['user_id' => $userId]);
    }

    /**
     * @param int $userId
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getOwner(int $tenantId, int $userId)
    {
        $userTenant = $this->userTenantRepo->getUserTenantsByTenantIdAndUserId($tenantId, $userId);

        return $this->addUserTenantRelations($userTenant)->first();
    }

    /**
     * @param int $tenantId
     *
     * @return Collection
     */
    public function getUserTenantWithRelationsByTenantId(int $tenantId)
    {
        $userTenants = $this->userTenantRepo->getUserTenantsByTenantId($tenantId);

        return $this->addUserTenantRelations($userTenants);
    }

    /**
     * @param int   $tenantId
     * @param array $userIds
     *
     * @return Collection
     */
    public function getUserTenantWithRelationsByUserIds(int $tenantId, array $userIds)
    {
        $userTenants = $this->userTenantRepo->getUserTenantsByTenantIdAndUserIds($tenantId, $userIds);

        return $this->addUserTenantRelations($userTenants);
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getUserTenantWithRelationsById(int $userTenantId)
    {
        $userTenants = $this->userTenantRepo->getUserTenantsById($userTenantId);

        return $this->addUserTenantRelations($userTenants)->first();
    }

    /**
     * @param array $userTenantIds
     *
     * @return Collection
     */
    public function getUserTenantWithRelationsByIds(array $userTenantIds)
    {
        $userTenants = $this->userTenantRepo->getUserTenantsByIds($userTenantIds);

        return $this->addUserTenantRelations($userTenants);
    }

    /**
     * @param Collection $userTenants
     *
     * @return Collection
     */
    private function addUserTenantRelations(Collection $userTenants)
    {
        $userTenantIds          = $userTenants->pluck('id')->unique()->toArray();
        $userIds                = $userTenants->pluck('user_id')->unique()->toArray();

        $users                  = app('UserRepo')->getUsersByIds($userIds);
        $roles                  = app('RoleRepo')->getRolesByUserTenantIds($userTenantIds);

        $userTenantGroups       = app('UserTenantGroupRepo')->getUserTenantGroupsByUserTenantIds($userTenantIds);
        $userTenantGroupsIds    = $userTenantGroups->pluck('id')->unique()->toArray();

        $userTenantGroupRoles   = app('UserTenantGroupRepo')->getUserTenantGroupRolesByIds($userTenantGroupsIds);

        $userTenantGroups = UserTenantGroupCollection::make($userTenantGroups)->setAttributes([
            'roles' => $userTenantGroupRoles->groupBy('user_tenant_group_id')
        ], true);

        $userTenants = UserTenantCollection::make($userTenants)
            ->setRoles($roles)
            ->setUsers($users)
            ->setUserTenantGroups($userTenantGroups->groupBy('user_tenant_id'));

        return $userTenants;
    }

    /**
     * @return Collection
     */
    public function getAvailableRoles()
    {
        return app('RoleRepo')->getAvailableRoles(Role::AVAILABLE_TENANT_LEVEL_ROLES);
    }

    /**
     * @param int $userTenantId
     * @param int $tenantId
     *
     * @return bool
     */
    public function deleteTenantMemberById(int $userTenantId, int $tenantId) : bool
    {
        /** @var UserTenant $member */
        $member = app('UserTenantRepo')->find($userTenantId);

        if ($member->tenant_id === $tenantId) {
            $member->delete();
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getAllTenants()
    {
        return $this->tenantRepo->getAllTenants();
    }

    /**
     * @param array      $attributes
     * @param UserTenant $userTenant
     * @param bool       $canInvited
     *
     * @return mixed
     * @throws \Throwable
     */
    public function updateMember(array $attributes, UserTenant $userTenant, bool $canInvited = false)
    {
        return DB::transaction(function () use ($attributes, $userTenant, $canInvited) {
            app('UserRepo')->update($attributes, $userTenant->user->id);

            if (isset($attributes['password']) && !is_null($attributes['password'])) {
                app('UserRepo')->changePassword($attributes['password'], $userTenant->user->id);
            }

            app('UserTenantRepo')->update($attributes, $userTenant->id);

            app('GroupSer')->detachUserTenantAllGroups($userTenant);
            app('GroupSer')->attachUserToGroupWithRoles($attributes['group_roles'], $userTenant);

            $roles = !empty($attributes['roles']) ? $attributes['roles']: [];
            app('AuthSer')->attachToUserTenantRole($userTenant, $roles, $canInvited);
        });
    }







    public function markTenantAsChosen(int $userId, int $tenantId)
    {
        /** @var User $user */
        $user = app('UserRepo')->find($userId);
        $user->chosen_tenant_id = $tenantId;
        $user->save();
    }

    public function getTenantsByUserId(int $userId, bool $withOwn = false) : Collection
    {
        $tenants = app('UserRepo')->find($userId)->tenants();
        if (!$withOwn) {
            $tenants = $tenants->wherePivot('is_owner', 0);
        }
        return $tenants->get();
    }

    public function getUserTenantRoleById(int $userTenantRoleId)
    {
        return app('UserTenantRoleRepo')->find($userTenantRoleId);
    }

    public function getMemberById(int $userTenantId)
    {
        return app('UserTenantRepo')->find($userTenantId);
    }

    public function getTenantById(int $tenantId)
    {
        return app('TenantRepo')->find($tenantId);
    }

    public function changeCurrentTenant(int $tenantId, int $userId)
    {
        $user = app('UserRepo')->find( $userId);
        $user->chosen_tenant_id = $tenantId;
        $user->save();
    }

    public function deleteTenant(Tenant $tenant)
    {
        /** @var User $owner */
        $owner = $tenant->owner();

        $groups = $owner->user_tenant->groups;
        $boards = Board::whereIn('group_id', $groups->pluck('id'))->with('tasks')->get();

        foreach($boards as $board) {
            if ($this->boardService->destroyOrArchiveBoard($board->id)) {
                $this->priorityService->removePriorityByBoardId($board->id);
            }
        }

        $tenant->subscription()->delete();
        $tenant->pipelines()->delete();
        $tenant->customRoles()->delete();
        $tenant->customPriorities()->delete();

        $tenant->users->each(function(User $user) {
            if($user->tenants->count() == 1) {
                $user->delete();
            }
        });

        $tenant->delete();
    }
}
