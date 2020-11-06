<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'              => 'required|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
            'last_name'         => 'required|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
            'nickname'          => 'nullable|min:1|max:255|regex:/^[\pL\pM\pN\s.\_-]+$/ui',
            'avatar'            => 'sometimes',
        ]; 
    }

    public function messages()
    {
        return [
            'name.regex'        => 'The user name can include Latin letters (a-z), numbers (0-9) and a period (.)',
            'last_name.regex'   => 'The user last name can include Latin letters (a-z), numbers (0-9) and a period (.-)',
            'nickname.regex'    => 'The screen name can include Latin letters (a-z), numbers (0-9) and a period',
        ];
    }
}
