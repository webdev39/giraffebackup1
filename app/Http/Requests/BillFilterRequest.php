<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillFilterRequest extends FormRequest
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
            'selectBoards'      => 'nullable|array',
            'selectPeriod'      => 'nullable|array',
            'selectTimeFilter'  => 'nullable|array',
            'selectMembers'     => 'nullable|array',
            'selectBoards.*'    => 'integer',
            'selectPeriod.*'    => 'integer',
            'selectTimeFilter.*'=> 'integer',
            'selectMembers.*'   => 'integer',
            'customTimerange'   => 'nullable|array'
        ];
    }
}
