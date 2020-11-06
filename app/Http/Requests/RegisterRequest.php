<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email'     => 'required|min:6|email|unique:users,email',
            'password'  => 'required|min:6|max:30|regex: /^\S*$/',
            'name'      => 'required|alpha|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
            'last_name' => 'required|alpha|min:1|max:255|regex:/^[\pL\pM\pN\s.-]+$/ui',
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.regex'            => 'The user name can include Latin letters (a-z), numbers (0-9) and a period (.-)',
            'last_name.regex'       => 'The user last name can include Latin letters (a-z), numbers (0-9) and a period (.-)',
        ];
    }
}
