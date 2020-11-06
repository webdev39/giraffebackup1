<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetachRoleToUserGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_id'           => 'required|integer',
            'group_id'          => 'required|integer',
            'user_tenant_id'    => 'required|integer'
        ];
    }
}
