<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreatePriorityRequest;
use App\Http\Requests\UpdatePriorityRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSortOrderPriorityRequest;
use App\Http\Resources\BoardResource;
use App\Http\Resources\PriorityResource;
use App\Models\Priority;
use App\Models\UserTenant;
use App\Services\Priority\PriorityService;
use Illuminate\Support\Facades\Auth;

class PriorityController extends Controller
{
    /** @var PriorityService */
    private $priorityService;

    /**
     * PriorityController constructor.
     */
    public function __construct()
    {
        $this->priorityService = app('PrioritySer');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $priorities        = $this->priorityService->getPrioritiesByUserTenantId(Auth::userTenantId());
        $prioritiesDefault = $priorities->where('is_default', 1)->values();
        $prioritiesCustom  = $priorities->where('is_default', 0)->values();

        return response()->json([
            'priorities'  => [
                'default'  => PriorityResource::collection($prioritiesDefault),
                'custom'   => PriorityResource::collection($prioritiesCustom),
            ]
        ]);
    }

    /**
     * @param CreatePriorityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(CreatePriorityRequest $request)
    {
        $priorities = $this->priorityService->getPrioritiesByBoardId($request->get('board_id'), Auth::userTenantId());

        if ($priorities->where('name',  $request->get('name'))->count()) {
            abort(403, 'You already have priority with this name');
        }

        $priority = $this->priorityService->createOrUpdatePriority($request->all());
        $priority = $this->priorityService->getPriorityWithRelationsById($priority->id, Auth::userTenantId());

        return response()->json([
            'priority' => new PriorityResource($priority)
        ]);
    }

    /**
     * @param UpdatePriorityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePriorityRequest $request)
    {
        $priority = $this->priorityService->getPrioritiesByBoardId($request->get('board_id'), Auth::userTenantId())
            ->where('id', '!=', $request->get('priority_id'))
            ->where('name', $request->get('name'))
            ->first();

        if ($priority) {
            abort(403, 'You already have priority with this name');
        }

        $priority = $this->priorityService->createOrUpdatePriority($request->all(), $request->get('priority_id'));

        if ($request->exists('is_invisible')) {
            $this->priorityService->updateInvisible($priority->id, Auth::userTenantId(), $request->get('is_invisible'));
        }

        $priority = $this->priorityService->getPriorityWithRelationsById($priority->id, Auth::userTenantId());

        return response()->json([
            'priority' => new PriorityResource($priority)
        ]);
    }

    /**
     * @param UpdateSortOrderPriorityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSortOrder(UpdateSortOrderPriorityRequest $request)
    {
        $this->priorityService->updateTaskOrders($request->get('order'));

        return response()->json(['message' => 'Success']);
    }

    /**
     * @param int $priorityId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function remove(int $priorityId)
    {
        /** @var Priority $priority */
        $priority   = $this->priorityService->getPriorityModelById($priorityId);
        $board      = $priority->board()->first();

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!Auth::user()->can('managePriority', [$priority, $board])) {
            abort(403, 'User has no permission to remove the priority');
        }

        if ($priority->is_primary) {
            abort(403, 'You cannot delete the default priority');
        }

        $isUpdate = app('TaskSer')->setPrimaryPriority($board->id, $priority->id);

        $this->priorityService->remove($priority->id);

        if ($isUpdate) {
            $board = app('BoardSer')->getBoardWithRelationsById($board->id, $userTenant->id);

            return response()->json([
                'board' => new BoardResource($board),
            ]);
        }

        return response()->json([
            'board' => null,
        ]);
    }
}
