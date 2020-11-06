<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePipelineRequest
 *
 * @package App\Http\Requests
 *
 * @author  LexXurio
 */
class UpdatePipelineRequest extends FormRequest
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
            'pipeline_id' => 'required|integer',
            'name'        => 'required|string|max:50',
            'description' => 'max:255|sometimes',
            'host'        => 'required|string|max:255',
            'port'        => 'required|integer|max:999',
            'encryption'  => 'required|min:3|max:4',
            'email'       => 'required|min:6|email|unique:pipelines,email,'.$this->pipeline_id,
            'password'    => 'max:255|regex: /^\S*$/|sometimes',
            'is_active'   => 'required|boolean',
        ];
    }
}
