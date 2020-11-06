<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'id'            => 'required|integer|exists:customers,id',
            'name'          => 'required|string|min:1|max:127',
            'contact'       => 'nullable|string|min:1|max:127',
            'custom_id'     => 'required|string|min:1|max:127',
            'city'          => 'required|string|min:1|max:127',
            'country_id'    => 'required|integer',
            'email'         => 'nullable|email',
            'telephone'     => 'nullable|string|min:3|max:22',
            'street'        => 'required|string|min:1|max:127',
            'postcode'      => 'nullable|string',
            'house'         => 'required|string|min:1|max:63',
            'hourly_rate'   => 'required|between:0,9999999.99',
        ];
    }
}
