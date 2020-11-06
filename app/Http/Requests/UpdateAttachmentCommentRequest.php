<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttachmentCommentRequest extends FormRequest
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
            'commentId'       => 'required|integer|exists:comments,id',
            'body'            => 'required|string',
            'mentions'        => 'nullable|array',
            'spatial'         => 'required|array',
            'spatial.x'       => 'required|integer',
            'spatial.y'       => 'required|integer',
            'spatial.w'       => 'required|integer',
            'spatial.h'       => 'required|integer',
        ];
    }
}
