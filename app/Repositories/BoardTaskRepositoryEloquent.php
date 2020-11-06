<?php

namespace App\Repositories;

use App\Models\BoardTask;

/**
 * Class BoardTaskRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @property $model BoardTask
 */
class BoardTaskRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BoardTask::class;
    }

    /**
     * @param int $boardId
     * @param int $taskId
     *
     * @return mixed
     */
    public function attachToOtherBoard(int $boardId, int $taskId)
    {
        return $this->model->where('task_id', $taskId)->update(['board_id' => $boardId]);
    }
}
