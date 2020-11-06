<?php

namespace App\Http\Resources;

/**
 * Class UserProfileResource
 *
 * @package App\Http\Resources
 *
 * @property int         $language_id
 * @property int         $font_id
 * @property string      $primary_color
 * @property string      $secondary_color
 * @property string      $background
 * @property array       $notify_types
 * @property string      $time_zone
 * @property string      $tour
 * @property string      $audio
 */
class UserProfileResource extends BaseResource
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

        $result = array_merge((new UserResource($this->resource))->resolve(), [
            'language_id'       => $this->language_id,
            'font_id'           => $this->font_id,
            'primary_color'     => $this->primary_color,
            'secondary_color'   => $this->secondary_color,
            'background'        => $this->background ? url($this->background) : null,
            'time_zone'         => $this->time_zone,
            'notify_types'      => $this->getCollectionColumn($this->notify_types, 'id'),
            'can_invited'       => $this->getCanInvited(),
            'tour'              => $this->tour,
            'audio'             => $this->audio,
        ]);

        return $result;
    }

    /**
     * @return bool
     */
    private function getCanInvited() : bool
    {
        return $this->can_invited;
    }
}
