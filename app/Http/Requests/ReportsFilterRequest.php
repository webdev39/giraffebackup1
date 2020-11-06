<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportsFilterRequest extends FormRequest
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
            'selectedItems.selectBoards'      => 'nullable|array',
            'selectedItems.selectMembers'      => 'nullable|array',
            'selectedItems.selectClients'      => 'nullable|array',
            'selectedItems.selectTimeranges'      => 'nullable|array',
            'selectedItems.selectShowOptions'      => 'nullable|array',
            'selectedItems.selectGrouping'      => 'nullable|array',
            'selectedItems.selectDetails'      => 'nullable|array',
            'customTimerange'      => 'nullable|array',
        ];
    }
}
