<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserColorSchemeRequest extends FormRequest
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
            'sidebar' => [
                'required',
                'array'
            ],
            'navbar' => [
                'required',
                'array'
            ],
            'task_detail' => [
                'required',
                'array'
            ],
            'manage' => [
                'required',
                'array'
            ],
            'subscribers' => [
                'required',
                'array'
            ],
            'management' => [
                'required',
                'array'
            ],
            'modal' => [
                'required',
                'array'
            ],
            'buttons' => [
                'required',
                'array'
            ],
        ];
    }
}
