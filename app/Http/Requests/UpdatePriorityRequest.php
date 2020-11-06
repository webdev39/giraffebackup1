<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriorityRequest extends FormRequest
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
            'priority_id' => 'required|integer',
            'board_id'    => 'required|exists:boards,id',
            'name'        => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:255',
            'color'       => 'nullable|string',
            'is_invisible'=> 'nullable|boolean',
        ];
    }
}
