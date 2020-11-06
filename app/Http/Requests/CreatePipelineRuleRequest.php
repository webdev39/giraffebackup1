<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreatePipelineRequest
 *
 * @package App\Http\Requests
 *
 * @author  LexXxurio
 */
class CreatePipelineRuleRequest extends FormRequest
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
            'pipeline_id'       => 'required|int',
            'pipeline_filter_id'=> 'required|int',
            'boards'            => 'required|array',
            'name'              => 'required|string|max:50',
            'description'       => 'max:255|sometimes',
            'keywords'          => 'array'
        ];
    }
}
