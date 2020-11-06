<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
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
            'group_id'                  => 'required|integer',
            'name'                      => 'required|string|max:150',
            'description'               => 'nullable|string|min:1|max:255',
//            'members'                   => 'nullable|array',
//            'members.*.role_id'         => 'required|integer',
//            'members.*.user_tenant_id'  => 'required|integer',
        ];
    }
}
