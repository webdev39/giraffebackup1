<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBillRequest extends FormRequest
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
            'customerId'                        => 'integer',
            'rate'                              => 'regex:/^\d*(\.\d{1,2})?$/',
            'billTimers'                        => 'array',
            'billTimers.*.timerBillingId'       => 'required|integer',
            'billTimers.*.timerBillingStatusId' => 'required|integer'
        ];
    }
}
