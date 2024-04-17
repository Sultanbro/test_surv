<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BonusUpdateRequest extends FormRequest
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
            'title'      => 'string|required',
            'sum'     => 'numeric|required',
            'group_id'       => 'integer|required',
            'activity_id'       => 'required',
            //'unit'            => 'string',
            'quantity'            => 'integer|required',
            //'daypart'            => 'integer',
           // 'text'            => 'string',
        ];
    }
}
