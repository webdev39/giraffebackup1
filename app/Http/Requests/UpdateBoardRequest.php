<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
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
            'board_id'          => 'required|integer',
            'group_id'          => 'required|integer',
            'name'              => 'required|string|min:1|max:255',
            'description'       => 'nullable|string|min:1|max:255',
            'deadline'          => 'nullable',
            'budget_id'         => 'nullable|integer',
            'budget_type_id'    => 'nullable|integer',
            'soft_budget'       => 'nullable|string',
            'hard_budget'       => 'nullable|string',
            'view_type_id'      => 'nullable|integer',
            'priority_id'       => 'nullable|integer',
            'hide_done_tasks'   => 'nullable|boolean',
            'quick_nav'         => 'nullable|boolean',
        ];
    }
}
