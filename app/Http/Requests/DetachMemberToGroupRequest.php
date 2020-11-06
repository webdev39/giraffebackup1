<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetachMemberToGroupRequest extends FormRequest
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
            'group_id'              => 'required|integer',
            'user_tenant_ids'       => 'required|array',
            'user_tenant_ids.*'     => 'required|integer',
        ];
    }
}
