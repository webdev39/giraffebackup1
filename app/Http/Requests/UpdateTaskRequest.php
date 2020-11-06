<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'task_id'           => 'required|integer',
            'board_id'          => 'nullable|integer',
            'priority_id'       => 'nullable|integer',
            'name'              => 'required_if:is_draft,0|nullable|string|min:1|max:255',
            'description'       => 'nullable|string',
            'deadline'          => 'nullable|date_format:Y-m-d H:i:s',
            'planned_deadline'  => 'nullable|date_format:Y-m-d H:i:s',
            'soft_budget'       => 'nullable|string',
            'hard_budget'       => 'nullable|string',
            'budget_type_id'    => 'nullable|integer',
            'repeat_unit'       => 'nullable|string',
            'repeat_interval'   => 'nullable|integer',
            'repeat_started_at' => 'nullable|date_format:Y-m-d H:i:s',
            'repeat_ended_at'   => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
