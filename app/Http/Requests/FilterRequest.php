<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'name'              => 'required|string|max:100',
            'range'             => 'nullable|string',
            'assigner_ids'      => 'nullable|array',
            'assigner_ids.*'    => 'integer',
            'group_ids'         => 'nullable|array',
            'group_ids.*'       => 'integer',
            'board_ids'         => 'nullable|array',
            'board_ids.*'       => 'integer',
            'priority_ids'      => 'nullable|array',
            'priority_ids.*'    => 'integer',
            'view_type_id'      => 'integer',
            'status'            => 'nullable|boolean',
            'filter_id'         => 'nullable|integer'
        ];
    }

    /**
     * Validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The title field is required',
            'name.string' => 'The title field must be a string',
            'name.max' => 'The title may not be greater than 100 characters'
        ];
    }
}
