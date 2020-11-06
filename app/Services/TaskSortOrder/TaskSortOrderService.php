<?php

namespace App\Services\TaskSortOrder;

use App\Models\Permission;
use App\Models\TaskSortOrder;
use App\Repositories\TaskSortOrderRepositoryEloquent;
use App\Services\BaseService;
use App\Services\UserTask\UserTaskService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TaskSortOrderService extends BaseService
{
    /** @var TaskSortOrderRepositoryEloquent */
    private $userTaskSortOrderRepo;

    /**
     * TaskSortOrder constructor.
     */
    public function __construct()
    {
        $this->userTaskSortOrderRepo = app('TaskSortOrderRepo');
    }

    /**
     * @param $name
     *
     * @return array
     */
    public static function getSortOrderTypeByName(string $name)
    {
        return array_first(array_filter(TaskSortOrder::SORT_ORDER_TYPES, function ($value) use ($name) {
            return $value['name'] == $name;
        }));
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function getSortOrderTypeByKey(string $key)
    {
        return TaskSortOrder::SORT_ORDER_TYPES[$key];
    }

    /**
     * @param array|null $sortType
     *
     * @return int|null
     */
    public static function getUserIdBySortType(array $sortType = null) : ?int
    {
        return is_null($sortType) || $sortType['private'] ? Auth::userTenantId() : null ;
    }

    /**
     * @param array $sortType
     * @param array $ids
     *
     * @return int|null
     */
    public static function getSortOrderModelId(array $sortType, array $ids) : ?int
    {
        if (isset($sortType['model'])) {
            $key = $sortType['model'].'_id';

            if (isset($ids[$key])) {
                return (int) $ids[$key];
            }
        }

        return null;
    }

    /**
     * @param array       $taskIds
     * @param string|null $sortBy
     * @param int|null    $modelId
     *
     * @return Collection|null
     */
    public function getSortOrderByTaskIds(array $taskIds, string $sortBy = 'list', int $modelId = null) : ?Collection
    {
        if ($sortType = self::getSortOrderTypeByName($sortBy)) {
            $userId = self::getUserIdBySortType($sortType);

            return $this->userTaskSortOrderRepo->getSortOrderByTaskIds($taskIds, $userId, $sortType['name'], $modelId);
        }

        return null;
    }

    /**
     * @param array $sortType
     * @param int   $modelId
     * @param array $orders
     *
     * @return mixed
     * @throws \Throwable
     */
    public function updateTaskOrders(array $sortType, int $modelId = null, array $orders = [])
    {
        $userId = self::getUserIdBySortType($sortType);

        return $this->userTaskSortOrderRepo->updateTaskOrders($sortType['name'], $orders, $userId, $modelId);
    }

    /**
     * @param int $taskId
     *
     * @return int
     */
    public function resetTaskOrderByTaskId(int $taskId)
    {
        return $this->userTaskSortOrderRepo->resetTaskOrderByTaskId($taskId);
    }
}