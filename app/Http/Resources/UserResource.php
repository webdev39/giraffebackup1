<?php

namespace App\Http\Resources;

use App\Models\UserColorScheme;
use App\Services\User\UserService;

/**
 * Class UserResource
 *
 * @package App\Http\Resources
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $email
 * @property string     $last_name
 * @property string     $last_activity
 * @property string     $nickname
 * @property string     $avatar
 * @property integer    $user_tenant_id
 * @property integer    $tenant_id
 * @property boolean    $is_owner
 * @property boolean    $is_confirmed
 */
class UserResource extends BaseResource
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
            'name'          => $this->name,
            'email'         => $this->email,
            'last_name'     => $this->last_name,
            'status'        => $this->status,
            'nickname'      => UserService::getUserNickname($this->nickname, $this),
            'can_invited'   => $this->can_invited,
            'selected_color_scheme_id' => UserService::getColorSchema($this->id)->id,
        ];

        if ($request->get('user_res') != 'short') {
            $result = array_merge($result, [
                'is_owner'      => (bool) $this->is_owner,
                'is_confirmed'  => (bool) $this->is_confirmed,
                'tenant_id'     => $this->tenant_id,
                'user_tenant_id'=> $this->user_tenant_id,
                'last_activity' => $this->last_activity,
                'avatar'        => $this->avatar ? url($this->avatar) : null,
            ]);
        }

        return $result;
    }
}
