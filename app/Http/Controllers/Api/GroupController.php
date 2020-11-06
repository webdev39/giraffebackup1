<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AttachMemberToGroupRequest;
use App\Http\Requests\AttachRoleToUserGroup;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\DetachMemberToGroupRequest;
use App\Http\Requests\DetachRoleToUserGroup;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserTenantResource;
use App\Models\Group;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTenant;
use App\Services\Group\GroupService;
use App\Services\Role\RoleService;
use App\Services\Tenant\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * @var GroupService
     */
    public $groupService;
    /**
     * @var RoleService
     */
    private $roleService;
    /**
     * @var TenantService
     */
    private $tenantService;

    /**
     * GroupController constructor.
     * @param GroupService $groupService
     * @param RoleService $roleService
     * @param TenantService $tenantService
     */
    public function __construct(GroupService $groupService, RoleService $roleService, TenantService $tenantService)
    {
        $this->groupService = $groupService;
        $this->roleService = $roleService;
        $this->tenantService = $tenantService;
    }

    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $isProfilerEnabled = enable_profiler();

        $groups = $this->groupService->getGroupsWithRelationsByUserTenantId(Auth::userTenantId(), request('isArchived', null));

        $response = response()->json([
            'groups'        => GroupResource::collection($groups),
            'permissions'   => PermissionResource::collection($groups->permissions),
        ]);

        if($isProfilerEnabled) {
            run_profiler('GroupController@index');
        }

        return $response;
    }

    /**
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function all() : JsonResponse
    {
        $this->authorize('readAll', Group::class);

        $groups = $this->groupService->getGroupsWithRelationsByTenantId(Auth::userTenant()->tenant_id, request('isArchived', null));

        return response()->json([
            'groups'        => GroupResource::collection($groups),
            'permissions'   => PermissionResource::collection($groups->permissions),
        ]);
    }

    /**
     * @param int $groupId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $groupId) : JsonResponse
    {
        $group = $this->groupService->getGroupModelById($groupId);
        $this->authorize('show', $group);

        if (request()->get('group_relations') == 'none') {
            $group = $this->groupService->getGroupById($group->id);

            return response()->json([
                'group' => new GroupResource($group),
            ]);
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id, Auth::userTenantId());
        $groupPermissions = empty($group->permissions) ? collect([]) : $group->permissions;

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($groupPermissions),
        ]);
    }

    /**
     * @param int $groupId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function cloneGroup(int $groupId) : JsonResponse
    {
        $group = $this->groupService->getGroupModelById($groupId);
        $this->authorize('cloneGroup', $group);

        $group = $this->groupService->cloneGroup($groupId);

        if (request()->get('group_relations') == 'none') {
            $group = $this->groupService->getGroupById($group->id);

            return response()->json([
                'group' => new GroupResource($group),
            ]);
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

    /**
     * @param CreateGroupRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(CreateGroupRequest $request)
    {
        $this->authorize('create', Group::class);

        if ($this->groupService->hasUserGroupWithName($request->get('name'), Auth::userTenantId())) {
            abort(422, 'User already has the group with the same name');
        }

        try {
            /** @var Group $group */
            $group = $this->groupService->createOrUpdateGroup($request->all(['name', 'description', 'members']), Auth::userTenant());
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
        
        if (request()->get('group_relations') == 'none') {
            $group = $this->groupService->getGroupById($group->id);

            return response()->json([
                'group' => new GroupResource($group),
            ]);
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

    /**
     * @param UpdateGroupRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UpdateGroupRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $group      = $this->groupService->getGroupModelById($request->get('group_id'));

        $this->authorize('update', $group);

        try {
            $group = $this->groupService->createOrUpdateGroup($request->all(['name', 'description']), $userTenant, $group->id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        if (request()->get('group_relations') == 'none') {
            $group = $this->groupService->getGroupById($group->id);

            return response()->json([
                'group' => new GroupResource($group),
            ]);
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

    /**
     * @param int $groupId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function destroy(int $groupId)
    {
        $group = $this->groupService->getGroupModelById($groupId);
        $this->authorize('destroy', $group);

        if ($this->groupService->checkTrackedTimeInGroup($groupId)) {
            $this->groupService->changeArchivedGroup($groupId, true);

            $group = $this->groupService->getGroupWithRelationsById($group->id);

            return response()->json([
                'is_removed'    => false,
                'group'         => new GroupResource($group),
                'permissions'   => PermissionResource::collection($group->permissions),
            ]);
        }

        $this->groupService->destroyGroup($group->id);

        return response()->json([
            'is_removed' => true
        ]);
    }

    /**
     * @param AttachMemberToGroupRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function attachUserToGroup(AttachMemberToGroupRequest $request)
    {
        $group = $this->groupService->getGroupModelById($request->get('group_id'));
        $this->authorize('manage', $group);

        try {
            $this->groupService->attachUserTenantToGroup($request->get('user_tenant_ids'), $group->id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

    /**
     * @param DetachMemberToGroupRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function detachUserToGroup(DetachMemberToGroupRequest $request)
    {
        $group = $this->groupService->getGroupModelById($request->get('group_id'));
        $this->authorize('manage', $group);

        if($group->members->count() == 1) {
            throw new \Exception(__('activity.group.detach_member_not_allowed'));
        }

        try {
            $this->groupService->detachUserTenantFromGroup($request->get('user_tenant_ids'), $group->id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection(optional($group)->permissions),
        ]);
    }

    /**
     * @deprecated
     * @param AttachRoleToUserGroup $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function attachRoleToGroupUser(AttachRoleToUserGroup $request)
    {
        $group  = $this->groupService->getGroupModelById($request->get('group_id'));
        $role   = $this->roleService->getCustomRoleById($request->get('role_id'));

        $this->authorize('manage', $group);

        $member = $this->tenantService->getUserTenantById($request->get('user_tenant_id'));
        $this->authorize('update', $member);

        $userTenantGroup = $this->groupService->getUserTenantGroup($member->id, $group->id);

        $this->groupService->attachRoleToGroupMember($userTenantGroup, $role);

        $member = $this->tenantService->getUserTenantWithRelationsById($member->id);
        $member->user_tenant_groups = $member->user_tenant_groups->groupBy('group_id')->map(function($group) {
            return $group->first();
        })->values();

        return response()->json([
            'member' => new UserTenantResource($member),
        ]);
    }

    /**
     * @deprecated
     * @param DetachRoleToUserGroup $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function detachRoleToGroupUser(DetachRoleToUserGroup $request)
    {
        $group  = $this->groupService->getGroupModelById($request->get('group_id'));
        $role   = $this->roleService->getCustomRoleById($request->get('role_id'));

        $this->authorize('manage', $group);

        $member = $this->tenantService->getUserTenantById($request->get('user_tenant_id'));
        $this->authorize('update', $member);

        $userTenantGroup = $this->groupService->getUserTenantGroup($member->id, $group->id);

        $this->groupService->detachRoleFromGroupMember($userTenantGroup, $role);

        $member = app('TenantSer')->getUserTenantWithRelationsById($member->id);

        return response()->json([
            'member' => new UserTenantResource($member),
        ]);
    }

    /**
     * @param int $groupId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function archived(int $groupId)
    {
        $group = $this->groupService->getGroupModelById($groupId);
        $this->authorize('destroy', $group);

        $this->groupService->changeArchivedGroup($groupId, true);

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

    /**
     * @param int $groupId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function unarchived(int $groupId)
    {
        $group = $this->groupService->getGroupModelById($groupId);
        $this->authorize('deleteGroupList', $group);

        $this->groupService->changeArchivedGroup($group->id, false);

        $group = $this->groupService->getGroupWithRelationsById($group->id);

        return response()->json([
            'group'         => new GroupResource($group),
            'permissions'   => PermissionResource::collection($group->permissions),
        ]);
    }

}
