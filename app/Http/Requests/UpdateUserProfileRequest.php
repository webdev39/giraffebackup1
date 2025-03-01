<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'background'        => 'sometimes',
            'primary_color'     => 'nullable|string',
            'secondary_color'   => 'nullable|string',
            'language_id'       => 'nullable|integer',
            'font_id'           => 'nullable|integer',
        ];
    }
}