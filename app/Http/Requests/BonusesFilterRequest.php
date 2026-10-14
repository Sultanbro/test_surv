<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BonusesFilterRequest extends FormRequest
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
            'targetable_type'   => 'numeric|nullable',
            'targetable_id'     => 'numeric|nullable',
            'user_id'           => 'numeric',
            'month'             => 'numeric|nullable',
            'year'              => 'numeric|nullable'
        ];
    }
}
