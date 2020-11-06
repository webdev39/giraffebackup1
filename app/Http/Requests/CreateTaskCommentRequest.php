<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskCommentRequest extends FormRequest
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
            'body'            => 'nullable|string',
            'attachments.*'   => 'integer|exists:attachments,id',
            'mentions'        => 'nullable|array',
            'parentId'        => 'nullable|integer',
        ];
    }
}
