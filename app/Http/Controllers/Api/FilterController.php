<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FilterRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\FilterResource;
use App\Http\Resources\TaskResource;
use App\Services\Filter\FilterService;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FilterController extends Controller
{
    /** @var FilterService */
    private $filterService;

    /** @var TaskService */
    private $taskService;

    /**
     * FilterController constructor.
     */
    public function __construct()
    {
        $this->filterService = app('FilterSer');
        $this->taskService = app('TaskSer');
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $filters = $this->filterService->getFiltersByUserTenantId(Auth::userTenantId());

        return response()->json([
            'filters' => FilterResource::collection($filters),
        ]);
    }

    /**
     * @param int $filterId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(int $filterId)
    {
        $filter = $this->filterService->getFilterById($filterId);

        if (!Auth::user()->can('update', $filter)) {
            abort(403, 'You have no permission to update this filter');
        }

        /** @var Collection $tasks */
        $filterTasks = $this->filterService->getTaskByFilterId($filterId);
        $tasks = $this->taskService->getTaskWithRelationsByIds($filterTasks->pluck('task_id')->toArray(), Auth::userTenantId());
        $tasks->setFilterSortOrder($filterTasks);

        return response()->json([
            'tasks'  => TaskResource::collection($tasks),
            'filter' => new FilterResource($filter)
        ]);
    }

    /**
     * @param FilterRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(FilterRequest $request)
    {
        $filter = $this->filterService->create($request->all(), Auth::userTenantId());

        /** @var Collection $tasks */
        $filterTasks = $this->filterService->getTaskByFilterId($filter->id);

        if (request('filter_task_res') == 'short') {
            return response()->json([
                'tasks'  => $filterTasks,
                'filter' => new FilterResource($filter)
            ]);
        }

        $tasks = $this->taskService->getTaskWithRelationsByIds($filterTasks->pluck('task_id')->toArray(), Auth::userTenantId());
        $tasks->setFilterSortOrder($filterTasks);

        return response()->json([
            'tasks'  => TaskResource::collection($tasks),
            'filter' => new FilterResource($filter)
        ]);
    }

    /**
     * @param FilterRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(FilterRequest $request)
    {
        $filter = $this->filterService->getFilterById($request->get('filter_id'));

        if (!$filter) {
            abort(404, 'Filter not found');
        }

        if (!Auth::user()->can('update', $filter)) {
            abort(403, 'You have no permission to update this filter');
        }

        $filter = $this->filterService->update($request->all(), $filter->id);

        /** @var Collection $tasks */
        $filterTasks = $this->filterService->getTaskByFilterId($filter->id);

        if (request('filter_task_res') == 'short') {
            return response()->json([
                'tasks'  => $filterTasks,
                'filter' => new FilterResource($filter)
            ]);
        }

        $tasks = $this->taskService->getTaskWithRelationsByIds($filterTasks->pluck('task_id')->toArray(), Auth::userTenantId());
        $tasks->setFilterSortOrder($filterTasks);

        return response()->json([
            'tasks'  => TaskResource::collection($tasks),
            'filter' => new FilterResource($filter)
        ]);
    }

    /**
     * @param int $filterId
     *
     * @return JsonResponse
     */
    public function destroy(int $filterId)
    {
        $filter = $this->filterService->getFilterById($filterId);

        if (!$filter) {
            abort(404, 'Filter not found');
        }

        if (!Auth::user()->can('update', $filter)) {
            abort(403, 'You have no permission to update this filter');
        }

        $this->filterService->remove($filter->id);

        return response()->json(['message' => 'Success']);
    }

}
