<?php

namespace App\Services\Repeat;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RepeatService extends BaseService
{
    private $repeatRepo;

    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->repeatRepo = app('RepeatRepo');
    }

    /**
     * @param      $attributes
     * @param null $taskId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrUpdateRepeat($attributes, $taskId = null)
    {
        if (is_null($attributes['time_unit'])) {
            return $this->repeatRepo->deleteWhere(['task_id' => $taskId]);
        }

        if (!is_null($attributes['started_at'])) {
            $attributes['started_at'] = Carbon::parse($attributes['started_at'])->startOfDay();
        }

        if (!is_null($attributes['ended_at'])) {
            $attributes['ended_at'] = Carbon::parse($attributes['ended_at'])->startOfDay();
        }

        return $this->repeatRepo->updateOrCreate(['task_id' => $taskId], $attributes);
    }
}
