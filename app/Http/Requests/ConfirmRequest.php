<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConfirmRequest
 *
 * @package App\Http\Requests
 */
class ConfirmRequest extends FormRequest
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
            'user_id'   => 'required|integer',
            'name'      => 'required|string|min:1|max:255',
            'type'      => 'required|string|min:1|max:255',
        ];
    }
}
