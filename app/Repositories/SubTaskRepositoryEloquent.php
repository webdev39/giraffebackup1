<?php

namespace App\Repositories;

use App\Models\SubTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubTaskRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SubTask::class;
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function buildSubTaskWithRelations()
    {
        return DB::table($this->subTaskTable)
            ->select([
                $this->subTaskTable.'.id',
                $this->subTaskTable.'.task_id',
                $this->subTaskTable.'.name',
                $this->subTaskTable.'.order',
                $this->subTaskTable.'.is_completed',
                $this->subTaskTable.'.created_at',
            ]);
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getSubTasksByTaskIds(array $taskIds) : Collection
    {
        return $this->buildSubTaskWithRelations()
            ->whereIn($this->subTaskTable.'.task_id', $taskIds)
            ->get();
    }

    /**
     * @param array $subTaskIds
     *
     * @return Collection
     */
    public function getSubTasksByIds(array $subTaskIds) : Collection
    {
        return $this->buildSubTaskWithRelations()
            ->whereIn($this->subTaskTable.'.id', $subTaskIds)
            ->get();
    }

    /**
     * @param int $subTaskId
     *
     * @return Collection
     */
    public function getSubTaskById(int $subTaskId)
    {
        return $this->buildSubTaskWithRelations()
            ->whereIn($this->subTaskTable.'.id', [$subTaskId])->firstOrFail();
    }

    /**
     * @param int   $taskId
     * @param array $orders
     *
     * @return mixed
     * @throws \Throwable
     */
    public function changeSubTaskOrder(int $taskId, array $orders)
    {
        return DB::transaction(function () use ($orders, $taskId) {
            foreach ($orders as $order => $subTaskId) {
                DB::table($this->subTaskTable)
                    ->where($this->subTaskTable.'.id', $subTaskId)
                    ->update([
                        $this->subTaskTable.'.order' => $order + 1
                    ]);
            }
        });
    }

    /**
     * @param array     $taskIds
     * @param bool|null $done
     *
     * @return Collection
     */
    public function getCountSubTasksByTaskIds(array $taskIds, bool $done = null) : Collection
    {
        $query = DB::table($this->subTaskTable)
            ->select([
                $this->subTaskTable.'.task_id',
                DB::raw('count(id) as total')
            ])
            ->whereIn($this->subTaskTable.'.task_id', $taskIds)
            ->groupBy($this->subTaskTable.'.task_id');

        if (!is_null($done)) {
            $query = $query->where($this->subTaskTable.'.is_completed', $done);
        }

        return $query->get();
    }
}