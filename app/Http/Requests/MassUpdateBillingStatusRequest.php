<?php

namespace App\Http\Requests;

use App\Models\BillingStatus;
use Illuminate\Foundation\Http\FormRequest;

class MassUpdateBillingStatusRequest extends FormRequest
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
            'timerBillingIds' => 'required|array',
            'timerBillingIds.*' => 'required|integer',
            'billingStatusId' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $billing_statuses = array_column(BillingStatus::INITIAL_STATUSES, 'id');
                    if (!in_array($value, $billing_statuses)) {
                        return $fail($attribute . ' is not valid status');
                    }
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'timerBillingIds.required' => 'Timers are required',
            'billingStatusId.required'  => 'A Billing Status is required',
            'timerBillingIds.*.integer' => 'Please choose at list one timer',
            'timerBillingIds.*.required' => 'Please choose at list one timer',
        ];
    }
}
