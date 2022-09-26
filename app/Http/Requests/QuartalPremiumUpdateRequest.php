<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuartalPremiumUpdateRequest extends FormRequest
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
            'activity_id'       => 'integer',
            'targetable_type'    => [
                'required',
                Rule::in([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
            ],
            'title'             => 'nullable|max:100',
            'text'              => 'nullable|max:255',
            'plan'              => 'nullable',
            'from'              => 'nullable|date',
            'to'                => 'nullable|date'
        ];
    }
}
