<?php

namespace App\Http\Responses;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PriorityResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserTenantResource;
use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserColorScheme;
use App\Models\UserTenant;
use App\Services\UserTask\UserTaskService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthResponse implements Responsable
{
    /** @var string  */
    private static $token;

    /** @var User  */
    private static $user;

    /** @var UserTenant */
    private static $userTenant;

    /**
     * AuthResponse constructor.
     *
     * @param string|null $token
     * @param User|null   $user
     */
    public function __construct(string $token = null, User $user = null)
    {
        self::$token  = $token ?? (string) JWTAuth::getToken();
        self::$user = $user ?? auth()->user();
        self::$userTenant = self::$user->getChosenTenant();
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toResponse($request) : JsonResponse
    {
        return response()->json($this->transform());
    }

    /**
     * A object transformer.
     *
     * @return array
     */
    protected function transform() : array
    {
        return [
            'members'               => self::getMembers(),
            'permissions'           => self::getUserPermissions(),
            'allPermissions'        => self::getAllPermissions(),
            'profile'               => self::getUserProfile(),
            'priorities'            => self::getPriorities(),
            'unread_notifications'  => self::getUnreadNotifications(),
            'tasks_deadline'        => self::getTasksDeadline(),
            'token'                 => self::getUserToken(),
            'settings'              => self::getSettings(),
            'colorSchemes'          => self::getColorSchemes(),
        ];
    }

    /**
     * @return AnonymousResourceCollection
     */
    private static function getMembers() : AnonymousResourceCollection
    {
        /** @var Tenant $members */
        $members = app('TenantSer')->getUserTenantWithRelationsByTenantId(self::$userTenant->tenant_id);
        $members = $members->map(function($member) {
            if(!empty($member->user_tenant_groups)) {
                $member->user_tenant_groups = $member->user_tenant_groups->groupBy('group_id')->map(function($group) {
                    return $group->first();
                })->values();
            }
            return $member;
        });


        return UserTenantResource::collection($members);
    }

    /**
     * @return AnonymousResourceCollection
     */
    private static function getUserPermissions() : AnonymousResourceCollection
    {
        if(self::$user->isOwner()) {
            $permissions = app('PermissionSer')->getPermissions();
        } else {
            $permissions = app('PermissionSer')->getAllUserPermissions(self::$user, self::$userTenant);
        }


        return PermissionResource::collection($permissions);
    }

    /**
     * @return AnonymousResourceCollection
     */
    private static function getAllPermissions() : AnonymousResourceCollection
    {
        $permissions = app('PermissionSer')->getPermissions();

        return PermissionResource::collection($permissions);
    }

    /**
     * @return UserProfileResource
     */
    private static function getUserProfile() : UserProfileResource
    {
        $userProfile = app('UserProfileSer')->getUserProfileWithRelationsByUserId(self::$user->id);
        return new UserProfileResource($userProfile);
    }

    /**
     * @return AnonymousResourceCollection
     */
    private static function getPriorities() : AnonymousResourceCollection
    {
        $priorities = app('PrioritySer')->getPrioritiesByUserTenantId(self::$userTenant->id);

        return PriorityResource::collection($priorities);
    }

    /**
     * @return AnonymousResourceCollection
     */
    private static function getUnreadNotifications() : AnonymousResourceCollection
    {
        return NotificationResource::collection(self::$user->unreadNotifications);
    }

    /**
     * @return array
     */
    private static function getTasksDeadline() : array
    {
        $result = [];

        foreach (UserTaskService::AVAILABLE_SLUGS as $slug => $days) {
            $result[$slug] = app('UserTaskSer')->getUserTaskActivity($slug, self::$userTenant->id, true);
        }

        return $result;
    }

    /**
     * @return null|string
     */
    private static function getUserToken() : ?string
    {
        return self::$token;
    }

    /**
     * @return \stdClass
     */
    private static function getSettings()
    {
        return app('TenantSer')->getCompanySettingsByUserTenant(self::$userTenant);
    }

    /**
     * @return \stdClass
     */
    private static function getColorSchemes()
    {
        $colorSchemes = self::$user->colorSchemes;
        UserColorScheme::getDefaultColorScheme()->each(function ($item, $key) use ($colorSchemes) {
            $colorSchemes->prepend($item);
        });
        return $colorSchemes;
    }
}
