<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AttachRoleRequest;
use App\Http\Requests\DetachRoleRequest;
use App\Http\Requests\SettingsRequest;
use App\Http\Requests\TemplateRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\SettingsResource;
use App\Http\Resources\TenantResource;
use App\Http\Resources\UserTenantResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Http\Controllers\Controller;
use App\Models\UserTenant;
use App\Policies\V2\UserTenantPolicy;
use App\Services\AuthService;
use App\Services\Role\RoleService;
use App\Services\Tenant\TenantService;
use App\Services\UserProfile\UserProfileService;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TenantController
 *
 * @package App\Http\Controllers\Api
 */
class TenantController extends Controller
{
    /** @var \App\Services\Tenant\TenantService */
    private $tenantService;

    /** @var \App\Services\Role\RoleService */
    private $roleService;
    /**
     * @var UserProfileService
     */
    private $profileService;
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * TenantController constructor.
     * @param TenantService $tenantService
     * @param RoleService $roleService
     * @param UserProfileService $profileService
     * @param AuthService $authService
     */
    public function __construct(
        TenantService $tenantService,
        RoleService $roleService,
        UserProfileService $profileService,
        AuthService $authService
    )
    {
        $this->tenantService = $tenantService;
        $this->roleService   = $roleService;
        $this->profileService = $profileService;
        $this->authService = $authService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if ($userTenant && $userTenant->is_owner === 0) {
            abort(403, "Sorry you haven't permission to see tenants");
        }

        $tenants = $this->tenantService->getAllTenants();

        return response()->json([
            'tenants' => TenantResource::collection($tenants)
        ]);
    }

    /**
     * @return UserTenantResource
     */
    public function own()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $owner = $this->tenantService->getOwner($userTenant->tenant_id, Auth::id());

        return new UserTenantResource($owner);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function members()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->authorize('manageUsers', $userTenant);

        /** @var Tenant $members */
        $members = $this->tenantService->getUserTenantWithRelationsByTenantId($userTenant->tenant_id);

        return response()->json([
            'members' => UserTenantResource::collection($members)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function roles()
    {
        $roles = Role::where('is_one_permission', true)->get();

        return response()->json([
            'roles' => RoleResource::collection($roles)
        ]);
    }

    /**
     * @param int $memberId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroyMember(int $memberId)
    {
        $this->authorize('manageUsers', Auth::userTenant());

        $isRemove = $this->tenantService->deleteTenantMemberById($memberId, Auth::id());

        if (!$isRemove) {
            abort(403, 'You have no permissions');
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param AttachRoleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(AttachRoleRequest $request)
    {
        $member = $this->profileService->getUserById($request->get('member_id'));
        $role   = $this->roleService->getCustomRoleById($request->get('role_id'));

        if (!$role) {
            abort(404, 'Role is not found');
        }

        if (!in_array($role->name, Role::AVAILABLE_TENANT_LEVEL_ROLES)) {
            abort(403, 'You don\'t have permissions to attach this role');
        }

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->authorize('manageUsers', Auth::userTenant());

        if (!Auth::user()->can('isMember', [$userTenant->tenant, $member])) {
            abort(403, 'It is not tenant member yet');
        }

        $this->roleService->attachRoleToMember($member->id, $userTenant->tenant_id, $role);

        $userTenant = $this->tenantService->getUserTenantWithRelationsById($member->id);

        return response()->json([
            'member' => new UserTenantResource($userTenant)
        ]);
    }

    /**
     * @param DetachRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function detachRoleFromMember(DetachRoleRequest $request)
    {
        $member = $this->profileService->getUserById($request->get('member_id'));
        $role   = $this->roleService->getCustomRoleById($request->get('role_id'));

        if (!$role) {
            abort(404, 'Role is not found');
        }

        if (!in_array($role->name, Role::AVAILABLE_TENANT_LEVEL_ROLES)) {
            abort(403, 'You don\'t have permissions to attach this role');
        }

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->authorize('manageUsers', Auth::userTenant());

        if (!Auth::user()->can('isMember', [$userTenant->tenant, $member])) {
            abort(403, 'It is not tenant member yet');
        }

        $this->roleService->detachRoleFromMember($member->id, $userTenant->tenant_id, $role);

        $userTenant = $this->tenantService->getUserTenantWithRelationsById($member->id);

        return response()->json([
            'member' => new UserTenantResource($userTenant)
        ]);
    }

    /**
     * @param int $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginUsingMemberId(int $memberId)
    {
        $member = $this->tenantService->getUserTenantById($memberId);

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!Auth::user()->can('isOwnerOfTenant', [$userTenant, $member])) {
            abort(403, 'This member does not belong to the current tenant');
        }

        $token = $this->authService->loginFromUser($member->user);

        return response()->json([
            'token' => $token
        ]);
    }

    /**
     * @param UpdateMemberRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateMember(UpdateMemberRequest $request)
    {
        $member = $this->tenantService->getUserTenantById($request->get('member_id'));

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $this->authorize('manageUsers', $userTenant);

        if ($member->user->email != $request->get('email')) {
            if ($this->authService->getUserByEmail($request->get('email'))) {
                abort(302, 'This email is already in use by another user');
            }
        }

        if ($member->user->name != $request->get('name') || $member->user->last_name != $request->get('last_name')) {
            if ($this->authService->checkUserName($request->get('name'), $request->get('last_name'))) {
                abort(302, 'User with this name is already exist');
            }
        }

        $attributes = $request->all([
            'name', 'last_name', 'email', 'status', 'password', 'group_roles', 'can_invited', 'company_name', 'roles'
        ]);

        try {
            $canInviteOthers = false;
            $canInviteOthersRole = Role::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->first();
            if(in_array($canInviteOthersRole->id, $attributes['roles'])) {
                $attributes['can_invited'] = true;
                $canInviteOthers = true;
            }
            $this->tenantService->updateMember($attributes, $member, $canInviteOthers);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        $userTenant = $this->tenantService->getUserTenantWithRelationsById($member->id);


        return response()->json([
            'member' => new UserTenantResource($userTenant)
        ]);
    }

    /**
     * @return mixed
     */
    public function indexSettings()
    {
        $settings =  $this->tenantService->getCompanySettingsByUserTenant(Auth::userTenant());

        return response()->json([
            'settings' => new SettingsResource($settings)
        ]);
    }

    /**
     * @param SettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateSettings(SettingsRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->tenantService->updateCompanySettings($request->all([
            'currency_id',
            'font_id',
            'logo',
            'email',
            'phone',
            'web',
            'postcode',
            'city',
            'street',
            'bill_settings',
            'fee',
            'creator',
            'author',
            'title',
            'subject',
            'keywords',
            'keywords',
            'filename',
            'date_format',
            'money_format',
        ]), $userTenant);

        $settings = $this->tenantService->getCompanySettingsByUserTenant($userTenant);
        
        return response()->json([
            'settings' => new SettingsResource($settings)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function indexTemplates()
    {
        $templates = $this->tenantService->getCompanyTemplates(Auth::userTenant());

        return response()->json([
            'templates' => $templates
        ]);
    }

    /**
     * @param TemplateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function updateTemplates(TemplateRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $this->tenantService->updateCompanyTemplate(
            $request->get('type'),
            $request->get('key'),
            $request->get('content'),
            $userTenant
        );

        $templates = $this->tenantService->getCompanyTemplates($userTenant);

        return response()->json([
            'templates' => $templates
        ]);
    }
}
