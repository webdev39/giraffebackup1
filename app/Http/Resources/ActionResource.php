<?php

namespace App\Http\Resources;

use App\Models\Permission;
use App\Models\Reactions;
use App\Services\Time\TimeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskActivityResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property int        $task_id
 * @property string     $task_name
 * @property string     $body
 * @property string     $source
 * @property Carbon     $updated_at
 * @property Carbon     $created_at
 * @property int        $board_id
 * @property string     $board_name
 * @property int        $group_id
 * @property string     $group_name
 * @property string     $field
 * @property \stdClass  $timer
 * @property \stdClass  $user
 * @property Collection $attachments
 * @property Collection $reactions
 *
 */
class ActionResource extends BaseResource
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
            'task_title'    => $this->task_name,
            'task_id'       => $this->task_id,
            'board_id'      => $this->board_id,
            'group_id'      => $this->group_id,
            'source'        => $this->source,
            'body'          => $this->body,
            'updated_at'    => (string) $this->updated_at,
            'created_at'    => (string) $this->created_at,
            'created_at_diff_for_humans' => ($this->created_at instanceof Carbon ? $this->created_at : Carbon::parse($this->created_at))->diffForHumans(),
            'canUpdate'     => (boolean) $this->checkPermission(Permission::READ_OTHER_COMMENTS_PERMISSION['name']),
            'canDelete'     => (boolean) $this->checkPermission(Permission::READ_OTHER_COMMENTS_PERMISSION['name']),
            'reactions'     => $this->getReactions(),
            'field'         => $this->field
        ];

        if ($this->source == 'comment') {
            $replies = isset($this->replies) ? $this->replies : $this->descendants;
            if(empty($replies)) {
                $replies = collect([]);
            }
            $repliesCount = count($replies);

            $result = array_merge($result, [
                'count_replies' => $repliesCount,
                'replies'       => $replies->map(function($comment) use($request) {
                    $reply = (new ActionResource($comment))->toArray($request);
                    $reply['parent_id'] = $comment->parent_id;
                    $reply['canUpdate'] = (boolean) $this->checkPermission(Permission::READ_OTHER_COMMENTS_PERMISSION['name']);
                    $reply['canDelete'] = (boolean) $this->checkPermission(Permission::READ_OTHER_COMMENTS_PERMISSION['name']);
                    $reply['replies'] = [];
                    return $reply;
                })
            ]);
        }

        if ($request->get('activity_log_res') != 'short') {
            if (isset($this->attachments)) {
                $result['attachments'] = AttachmentResource::collection($this->attachments);
            }

            if (isset($this->timer)) {
                $result['timer'] = new TimerResource($this->timer);
            }

            if (isset($this->user)) {
                $result['user'] = new UserResource($this->user);
                $result['user_id'] = $this->user->id;
            } else {
                $result['user_id'] = null;
                $result['user'] = [
                    'avatar' => null,
                    'can_invited' => 0,
                    'email' => 'bot@app.oc.plus',
                    'id' => null,
                    'is_confirmed' => true,
                    'is_owner' => true,
                    'last_activity' => now(),
                    'last_name' => '',
                    'name' => 'Bot',
                    'nickname' => 'bot',
                    'selected_color_scheme_id' => 1,
                    'status' => 1,
                    'tenant_id' => 1,
                    'user_tenant_id' => 1,
                ];
            }
        }

        if ($request->get('activity_log_res') == 'long') {
            $result = array_merge($result, [
                'task_name'         => $this->task_name,
                'board_name'        => $this->board_name,
                'group_name'        => $this->group_name,
            ]);
        }

        if($result['source'] === 'activity_log') {
            if(preg_match('/The.*changed\sdeadline\sof\stask\sfrom\s`(.+)`.*to\s`(.+)`$/', $result['body'], $matches)) {
                if (\DateTime::createFromFormat('Y-m-d H:i:s', $matches[1]) !== FALSE) {
                    $fromDate = Carbon::parse($matches[1])->addMinutes(Auth::utcOffset());
                    $todoDate = Carbon::parse($matches[2])->addMinutes(Auth::utcOffset());
                    $result['body'] = str_replace(
                        [$matches[1], $matches[2]],
                        [$fromDate->toDateTimeString(), $todoDate->toDateTimeString()],
                        $result['body']
                    );
                }
            } elseif(preg_match('/The.*changed\sdeadline\sof\stask\s.*to\s`(.+)`$/', $result['body'], $matches)) {
                $todoDate = Carbon::parse($matches[1])->addMinutes(Auth::utcOffset());
                $result['body'] = str_replace($matches[1], $todoDate->toDateTimeString(), $result['body']);
            } elseif(preg_match('/The.*changed\stodo\sof\stask\sto\s`(.+)`$/', $result['body'], $matches)) {
                $todoDate = Carbon::parse($matches[1])->addMinutes(Auth::utcOffset());
                $result['body'] = str_replace($matches[1], $todoDate->toDateTimeString(), $result['body']);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getReactions() : array
    {
        $result = [
            'like' => 0,
            'stick' => new \stdClass()
        ];

        if (!isset($this->reactions)) {
            return $result;
        }

        $reactions = $this->reactions->groupBy('reaction');

        /** @var Collection $likes */
        if ($likes = $reactions->get(Reactions::LIKE)) {
            $result['like'] = $likes->count();
        }

        /** @var Collection $sticks */
        if ($sticks = $reactions->get(Reactions::STICK)) {
            $result['stick'] = $sticks->mapWithKeys(function ($item) {
                return [$item->source_model . '_id' => $item->source_id];
            });
        }

        return $result;
    }
}
