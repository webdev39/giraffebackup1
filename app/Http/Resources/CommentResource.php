<?php

namespace App\Http\Resources;

use App\Models\Permission;
use App\Models\Reactions;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class CommentResource
 *
 * @package App\Http\Resources
 *
 * @property int        $id
 * @property int        $task_id
 * @property int        $parent_id
 * @property string     $task_name
 * @property string     $body
 * @property Carbon     $updated_at
 * @property Carbon     $created_at
 * @property int        $board_id
 * @property string     $board_name
 * @property int        $group_id
 * @property string     $group_name
 * @property \stdClass  $user
 * @property Collection $attachments
 * @property Collection $reactions
 * @property Collection $descendants
 */
class CommentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  array|\Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        $result = [
            'id'            => $this->id,
            'task_id'       => isset($this->task_id) ? $this->task_id : null,
            'board_id'      => isset($this->board_id) ? $this->board_id : null,
            'group_id'      => $this->group_id,
            'source'        => 'comment',
            'parent_id'     => $this->parent_id,
            'body'          => $this->body,
            'updated_at'    => (string) $this->updated_at,
            'created_at'    => (string) $this->created_at,
            'canUpdate'     => (boolean) $this->checkPermission(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']),
            'canDelete'     => (boolean) $this->checkPermission(Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION['name']),
            'reactions'     => $this->getReactions(),
        ];

        if (is_null($this->parent_id)) {
            $result['count_replies'] = 0;
            $result['replies'] = [];

            if (isset($this->descendants)) {
                $result['replies'] = CommentResource::collection($this->descendants);
                $result['count_replies'] = $this->descendants->count();
            }
        } else {
            $result['replies'] = [];
        }

        if (isset($this->spatial)) {
            $result['spatial'] = self::getSpatial(json_decode($this->spatial, true));
        }

        if ($request->get('comment_res') != 'short') {
            if (isset($this->attachments)) {
                $result['attachments'] = AttachmentResource::collection($this->attachments);
            }

            if (isset($this->user)) {
                $result['user'] = new UserResource($this->user);
            }
        }

        if ($request->get('comment_res') == 'long') {
            $result = array_merge($result, [
                'task_name'         => isset($this->task_name) ? $this->task_name : null,
                'board_name'        => isset($this->board_name) ? $this->board_name : null,
                'group_name'        => $this->group_name,
            ]);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getReactions() : array
    {
        $result   = [
            'like'  => 0,
            'stick' => 888
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
            $result['stick'] = $sticks->mapWithKeys(function($item) {
                return [$item->source_model.'_id' => $item->source_id];
            });
        }

        return $result;
    }

    /**
     * @param array $spatial
     *
     * @return array
     */
    private static function getSpatial(array $spatial): array
    {
        return array_merge(['x' => 0, 'y' => 0, 'w' => 0, 'h' => 0], $spatial);
    }
}
