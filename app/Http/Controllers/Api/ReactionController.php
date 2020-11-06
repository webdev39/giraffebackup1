<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserTenantResource;
use App\Models\UserTenant;
use App\Services\Reaction\ReactionService;
use Illuminate\Support\Facades\Auth;

class ReactionController
{
    /** @var ReactionService  */
    public $reactionService;

    /**
     * ReactionController constructor.
     */
    public function __construct()
    {
        $this->reactionService = app('ReactionSer');
    }

    /**
     * @param string $target
     * @param int    $targetId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function likers(string $target, int $targetId)
    {
        try {
            /** @var UserTenant $userTenant */
            $userTenant = Auth::userTenant();

            $likers = $this->reactionService->likers($userTenant->id, $target, $targetId);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }

        $members = app('TenantSer')->getUserTenantWithRelationsByIds($likers->pluck('user_tenant_id')->toArray());

        return response()->json([
            'members' => UserTenantResource::collection($members)
        ]);
    }

    /**
     * @param string $target
     * @param int    $targetId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(string $target, int $targetId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $this->reactionService->toggleLike($userTenant->id, $target, $targetId);

        return response()->json([
            'message'   => 'Success',
        ]);
    }

    /**
     * @param string $target
     * @param int    $targetId
     * @param string $source
     * @param int    $sourceId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stick(string $target, int $targetId, string $source, int $sourceId)
    {
        try {
            /** @var UserTenant $userTenant */
            $userTenant = Auth::userTenant();

            $this->reactionService->toggleStick($userTenant->id, $target, $targetId, $source, $sourceId);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }

        return response()->json([
            'message'   => 'Success',
        ]);
    }
}
