<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AttachRoleRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\DetachRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\UserTenant;
use App\Models\UserTenantRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserTenantGroupRole;

/**
 * Class RoleController
 *
 * @package App\Http\Controllers\Api
 */
class RoleController extends Controller
{

    /** @var \App\Services\Role\RoleService|\Illuminate\Foundation\Application|mixed */
    protected $roleService;

    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        $this->roleService = app('RoleSer');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $customRoles = $this->roleService->getCustomRolesByTenantId(Auth::user()->chosen_tenant_id);

        return response()->json([
            'roles' => RoleResource::collection($customRoles),
        ]);
    }

    /**
     * @param int $roleId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $roleId)
    {
        Auth::failIfHasNoPermission(Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION);

        $role = $this->roleService->getCustomRoleById($roleId);

        if (!$role) {
            abort(404, 'Role is not found');
        }

        if (!Auth::user()->can('update', $role)) {
            abort(403, 'User has no permissions to update this role');
        }

        return response()->json([
            'role' => new RoleResource($role)
        ]);
    }

    /**
     * @param CreateRoleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(CreateRoleRequest $request)
    {
        Auth::failIfHasNoPermission(Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION);

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if ($r = $this->roleService->existNameCustomRolesByTenantId($userTenant->tenant_id, $request->get('display_name'))) {
            abort(403, 'The role with this name already exists');
        }

        $permissions = app('PermissionSer')->getPermissionsByNames($request->get('permissions'));
        $permissions->push(Permission::whereName(Permission::READ_BOARD_PERMISSION['name'])->first());

        $role        = $this->roleService->createCustomRole($request->all(['display_name', 'description']), $permissions, $userTenant->tenant_id);

        return response()->json([
            'role' => new RoleResource($role)
        ]);
    }

    /**
     * @param UpdateRoleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request)
    {
        try {
            Auth::failIfHasNoPermission(Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION);

            $role = $this->roleService->getCustomRoleById($request->get('role_id'));

            if (!$role) {
                abort(404, 'Role is not found');
            }

            if (!Auth::user()->can('update', $role)) {
                abort(403, 'User has no permissions to update this role');
            }

            $permissions = app('PermissionSer')->getPermissionsByNames($request->get('permissions'));
            $this->roleService->updateCustomRole($request->all(['display_name', 'description']), $permissions, $request->get('role_id'));

            $role = $this->roleService->getCustomRoleById($role->id);

            return response()->json([
                'role' => new RoleResource($role)
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return abort(500, $ex->getMessage());
        }
    }

    /**
     * @param int $roleId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $roleId)
    {
        Auth::failIfHasNoPermission(Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION);

        $role = $this->roleService->getCustomRoleById($roleId);

        if (!$role) {
            abort(404, 'Role is not found');
        }

        if (!Auth::user()->can('update', $role)) {
            abort(403, 'User has no permissions to update this role');
        }

        if ((new UserTenantGroupRole())->where('role_id', $roleId)->first()) {
            abort(403, 'This role belongs to the user(s) and cannot be deleted');
        }

        if (!$this->roleService->deleteRole($roleId)) {
            abort('Role is not deleted');
        }

        return response()->json(['message' => 'Role is removed successfully']);
    }

}
