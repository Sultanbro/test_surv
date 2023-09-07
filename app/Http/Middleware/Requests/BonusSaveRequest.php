<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BonusSaveRequest extends FormRequest
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
            'targetable_id'      => 'integer|required',
            'targetable_type'    => [
                'required',
                Rule::in([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
            ],
            'title'         => 'array|required',
            'title.*'       => 'string|required',
            'sum'           => 'array|required',
            'sum.*'         => 'integer|required',
            'group_id'      => 'integer|required',
            'activity_id'   => 'array|required',
            'activity_id.*' => 'integer|required',
            'unit'          => 'array|required',
            'unit.*'        => 'string|required',
            'quantity'      => 'array',
            'quantity.*'    => 'integer|required',
            'daypart'       => 'array|required',
            'daypart.*'     => 'integer|required',
            'text'          => 'array|required',
            'text.*'        => 'string|required',
        ];
    }
}
