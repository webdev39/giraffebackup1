<?php

namespace App\Repositories;

use App\Models\TaskSortOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskSortOrderRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TaskSortOrder::class;
    }

    /**
     * @param array       $taskIds
     * @param int|null    $userId
     * @param string|null $sortTypeName
     * @param int|null    $modelId
     *
     * @return Collection
     */
    public function getSortOrderByTaskIds(array $taskIds, int $userId = null, string $sortTypeName = null, int $modelId = null) : Collection
    {
        $query = DB::table($this->userTaskSortOrderTable)
            ->select([
                $this->userTaskSortOrderTable.'.type              as type',
                $this->userTaskSortOrderTable.'.order             as order',
                $this->userTaskSortOrderTable.'.task_id           as task_id',
                $this->userTaskSortOrderTable.'.model_id          as model_id',
            ])
            ->where($this->userTaskSortOrderTable.'.user_id', $userId)
            ->where($this->userTaskSortOrderTable.'.model_id', $modelId)
            ->whereIn($this->userTaskSortOrderTable.'.task_id', $taskIds);

        if ($sortTypeName) {
            $query = $query->where($this->userTaskSortOrderTable.'.type', $sortTypeName);
        }

        return $query->get();
    }

    /**
     * @param string   $sortTypeName
     * @param array    $orders
     * @param int|null $userId
     * @param int|null $modelId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function updateTaskOrders(string $sortTypeName, array $orders, int $userId = null, int $modelId = null)
    {
        return DB::transaction(function () use ($sortTypeName, $orders, $userId, $modelId) {
            foreach ($orders as $order => $taskId) {
                DB::table($this->userTaskSortOrderTable)
                    ->updateOrInsert([
                        $this->userTaskSortOrderTable.'.user_id'   => $userId,
                        $this->userTaskSortOrderTable.'.task_id'   => $taskId,
                        $this->userTaskSortOrderTable.'.type'      => $sortTypeName,
                        $this->userTaskSortOrderTable.'.model_id'  => $modelId,
                    ],[
                        $this->userTaskSortOrderTable.'.order'     => $order + 1
                    ]);
            }
        });
    }

    /**
     * @param int $taskId
     *
     * @return int
     */
    public function resetTaskOrderByTaskId(int $taskId)
    {
        return DB::table($this->userTaskSortOrderTable)
            ->where($this->userTaskSortOrderTable.'.task_id', $taskId)
            ->delete();
    }
}