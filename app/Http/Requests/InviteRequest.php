<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
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
            'name'           => 'required|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
            'last_name'      => 'required|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
            'email'          => 'required|email',
            'tenant_id'      => 'required|integer',
            'without_verify' => 'boolean',
            'can_invited'    => 'boolean',
            'roles'          => 'array',
            'group_roles'    => 'required|array',
        ];
    }


    public function messages()
    {
        return [
            'name.regex'                => 'The user name can include Latin letters (a-z), numbers (0-9) and a period (.-)',
            'last_name.regex'           => 'The user last name can include Latin letters (a-z), numbers (0-9) and a period (.-)',
            'tenant_id.required'        => 'You should choose the company before invite the member!',
            'group_roles.required'      => 'Please select group and role for invited user',
            'group_roles.array'         => 'Group and role are not valid',
        ];
    }
}
