<?php

namespace App\Http\Resources;

use App\Services\Time\TimeService;
use Illuminate\Support\Collection;

/**
 * Class TimerResource
 *
 * @package App\Http\Resources
 * @property integer        $id
 * @property integer        $task_id
 * @property string         $task_name
 * @property string         $task_done_by
 * @property integer        $board_id
 * @property string         $board_name
 * @property integer        $group_id
 * @property string         $group_name
 * @property integer        $billing_status_id
 * @property integer        $timer_billing_id
 * @property integer        $user_tenant_id
 * @property string         $comment,
 * @property string         $start_time
 * @property string         $end_time
 * @property string         $created_at
 * @property string         $updated_at
 * @property Collection     $pauses
 */
class TimerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        $result = [
            'id'                => $this->id,
            'task_id'           => $this->task_id,
            'board_id'          => $this->board_id,
            'group_id'          => $this->group_id,
            'time'              => new TimeResource(TimeService::getSumTimeByTimer($this)),
            'comment'           => $this->comment,
            'start_time'        => $this->start_time,
            'end_time'          => $this->end_time,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];

        if ($request->get('timer_res') != 'short') {
            $result = array_merge($result, [
                'status'            => $this->getStatusTimer(),
                'billing_status_id' => $this->billing_status_id,
                'timer_billing_id'  => $this->timer_billing_id,
                'user_tenant_id'    => $this->user_tenant_id,
            ]);
        }

        if ($request->get('timer_res') == 'long') {
            $result = array_merge($result, [
                'task_name'         => $this->task_name,
                'board_name'        => $this->board_name,
                'group_name'        => $this->group_name,
            ]);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getStatusTimer() : string
    {
        if ($this->pauses) {
            foreach ($this->pauses as $pause) {
                if ($pause->end_time === null) {
                    return 'paused';
                }
            }
        }

        if ($this->start_time && $this->end_time === null) {
            return 'started';
        }

        return 'stopped';
    }
}
