<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuartalPremiumSaveRequest extends FormRequest
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
            'targetable_id'     => 'required|integer',
            'targetable_type'    => [
                'required',
                Rule::in([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
            ],
            'activity_id'       => 'nullable',
            'title'             => 'required|max:100',
            'text'              => 'required|max:255',
            'plan'              => 'required',
            'from'              => 'required|date',
            'to'                => 'required|date'
        ];
    }
}
