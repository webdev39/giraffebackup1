<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'currency_id'   => 'nullable|integer|exists:currencies,id',
            'font_id'       => 'nullable|integer|exists:fields,id',
            'title'         => 'nullable|string|min:1|max:255',
            'creator'       => 'nullable|string|min:1|max:255',
            'author'        => 'nullable|string|min:1|max:255',
            'subject'       => 'nullable|string|min:1|max:255',
            'keywords'      => 'nullable|string|min:1|max:255',
            'logo'          => 'nullable|string',
            'font_family'   => 'nullable|string|min:1|max:255',
            'fee'           => 'nullable|integer',
            'filename'      => 'nullable|string|min:1|max:255',
            'date_format'   => 'nullable|string|min:1|max:255',
            'money_format'  => 'nullable|array|size:2',
            'phone'         => 'nullable|string',
            'postcode'      => 'nullable|integer',
            'email'         => 'nullable|string',
            'street'        => 'nullable|string',
            'city'          => 'nullable|string',
            'web'           => 'nullable|string',
            'bill_settings' => 'nullable|array',
        ];
    }
}
