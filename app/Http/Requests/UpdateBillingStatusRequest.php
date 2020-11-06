<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 11.04.18
 * Time: 14:24
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingStatusRequest extends FormRequest
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
            'timerBillingId' => 'required|integer',
            'billingStatusId' => 'required|integer'
        ];
    }
}