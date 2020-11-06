<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class LogResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property int        $task_id
 * @property string     $task_name
 * @property string     $body
 * @property Carbon     $updated_at
 * @property Carbon     $created_at
 * @property int        $board_id
 * @property string     $board_name
 * @property int        $group_id
 * @property string     $group_name
 * @property \stdClass  $timer
 * @property \stdClass  $user
 * @property Collection $attachments
 */
class LogResource extends BaseResource
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
            'id'            => $this->id,
            'task_id'       => $this->task_id,
            'board_id'      => $this->board_id,
            'group_id'      => $this->group_id,
            'comment'       => $this->body,
            'updated_at'    => (string) $this->updated_at,
            'created_at'    => (string) $this->created_at,
            'canUpdate'     => (boolean) $this->checkPermission(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']),
            'canDelete'     => (boolean) $this->checkPermission(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']),
            'source'        => 'timer_log',
        ];

        if ($request->get('timer_log_res') != 'short') {
            if (isset($this->attachments)) {
                $result['attachments']    = AttachmentResource::collection($this->attachments);
            }

            if (isset($this->timer)) {
                $result['timer']          = new TimerResource($this->timer);
            }

            if (isset($this->user)) {
                $result['user']           = new UserResource($this->user);
            }
        }

        if ($request->get('timer_log_res') == 'long') {
            $result = array_merge($result, [
                'task_name'         => $this->task_name,
                'board_name'        => $this->board_name,
                'group_name'        => $this->group_name,
            ]);
        }

        return $result;
    }
}
