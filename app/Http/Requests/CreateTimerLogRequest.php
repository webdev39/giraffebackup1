<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTimerLogRequest extends FormRequest
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
            'time'              => ['nullable','string','regex:/(^(\d+)\:(\d+)\:(\d+)?$)/u'],
            'comment'           => ['nullable','string'],
            'mentions'          => ['nullable','array'],
            'attachments.*'     => ['integer','exists:attachments,id'],
            'start'             => ['nullable','boolean'],
            'logDate'           => ['nullable'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'time.regex'            => 'Invalid time format (example: `5:42:31`)',
        ];
    }
}
