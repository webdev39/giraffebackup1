<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBillRequest extends FormRequest
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
            'billId'                        => 'required|integer',
            'customerId'                    => 'required|integer',
            'customerRate'                  => 'required|numeric',
            'billDate'                      => 'required|string',
            'billLayoutTypeId'              => 'required|integer',
            'billStatus'                    => 'required|string',
            'billInvoiceNumber'             => 'integer',
            'billTimers'                    => 'required|array',
            'billTimers.*.billTimerId'      => 'present',
            'billTimers.*.billTimerRate'    => 'required|numeric',
            'billTimers.*.billTimerTime'    => 'required|string',
            'billTimers.*.billTimerComment' => 'nullable|string',
            'billTimers.*.billUnit'         => 'nullable|string',
        ];
    }
}
