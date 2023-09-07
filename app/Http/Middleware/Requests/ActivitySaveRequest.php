<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivitySaveRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'      => 'string|required',
            'group_id'      => 'integer|required',
            'daily_plan'      => 'required',
            'method'    => [
                'required',
                Rule::in([1,2,3,4,5,6]),
            ],
            'view'    => [
                'required',
                Rule::in([0,1,2,3,4,5,6]),
            ],
            'source'    => [
                'required',
                Rule::in([0,1,2,3]),
            ],
            'editable'      => 'integer',
            'order'         => 'integer',
            'weekdays'      => 'integer',
        ];
    }
}
