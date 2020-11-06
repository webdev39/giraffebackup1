<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 18.01.18
 * Time: 16:09
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskDescriptionRequest extends FormRequest
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
            'taskId'          => 'required|integer',
            'taskDescription' => 'nullable|string',
        ];
    }
}