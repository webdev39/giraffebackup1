<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            'name'              => 'required_if:is_draft,0|nullable|string|min:1|max:255',
            'board_id'          => 'required|integer',
            'is_draft'          => 'required|boolean',
            'draft_task_id'     => 'nullable|integer',
            'description'       => 'nullable|string',
            'deadline'          => 'nullable|string',
            'planned_deadline'  => 'nullable|string',
            'priority_id'       => 'nullable|integer',
        ];
    }
}
